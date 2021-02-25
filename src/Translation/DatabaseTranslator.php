<?php

declare(strict_types=1);

namespace BinSoul\Symfony\Bundle\I18n\Translation;

use BinSoul\Common\I18n\DefaultLocale;
use BinSoul\Symfony\Bundle\I18n\Repository\LocaleRepository;
use BinSoul\Symfony\Bundle\I18n\Repository\MessageRepository;
use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Translation\Translator as BaseTranslator;
use Symfony\Component\Translation\Exception\NotFoundResourceException;
use Symfony\Component\Translation\Formatter\MessageFormatterInterface;
use Symfony\Component\Translation\MessageCatalogue;
use Throwable;

/**
 * Replaces the original Symfony FrameworkBundle translator.
 */
class DatabaseTranslator extends BaseTranslator
{
    /**
     * @var MessageRepository
     */
    private $messageRepository;

    /**
     * @var LocaleRepository
     */
    private $localeRepository;

    /**
     * @var bool|null
     */
    private $tablesExist;

    /**
     * @var MessageFormatterInterface
     */
    private $messageFormatter;

    /**
     * @var string
     */
    private $defaultLocale;

    public function __construct(
        MessageRepository $messageRepository,
        LocaleRepository $localeRepository,
        ContainerInterface $container,
        MessageFormatterInterface $formatter,
        string $defaultLocale,
        array $loaderIds = [],
        array $options = [],
        array $enabledLocales = []
    ) {
        parent::__construct($container, $formatter, $defaultLocale, $loaderIds, $options, $enabledLocales);

        $this->messageRepository = $messageRepository;
        $this->localeRepository = $localeRepository;
        $this->messageFormatter = $formatter;
        $this->defaultLocale = $defaultLocale;
    }

    public function trans($id, array $parameters = [], $domain = null, $locale = null)
    {
        if ($id === null || $id === '') {
            return '';
        }

        if ($domain === null) {
            $domain = 'messages';
        }

        $result = parent::trans($id, $parameters, $domain, $locale);

        $isUntranslated = $result === $id || mb_strtolower($result) === mb_strtolower($id);

        if ($isUntranslated) {
            $catalogue = $this->load($locale ?? $this->defaultLocale, $domain);
            $locale = $catalogue->getLocale();

            while (! $catalogue->defines($id, $domain)) {
                $fallbackCatalogue = $catalogue->getFallbackCatalogue();

                if ($fallbackCatalogue === null) {
                    break;
                }

                $catalogue = $fallbackCatalogue;
                $locale = $catalogue->getLocale();
            }

            $result = $this->messageFormatter->format($catalogue->get($id, $domain), $locale ?? $this->defaultLocale, $parameters);
        }

        return $result;
    }

    public function load($locale, $domain = 'messages'): MessageCatalogue
    {
        if (! $this->isEnabled()) {
            return new MessageCatalogue($locale);
        }

        $localeEntity = null;
        $parsedLocale = DefaultLocale::fromString($locale, '_');

        while (! $parsedLocale->isRoot()) {
            $localeEntity = $this->localeRepository->findByCode($parsedLocale->getCode('-'));

            if ($localeEntity !== null) {
                break;
            }

            $parsedLocale = $parsedLocale->getParent();
        }

        if ($localeEntity === null) {
            throw new NotFoundResourceException(sprintf('The locale "%s" does not exist.', $locale));
        }

        try {
            $entities = $this->messageRepository->findAllByLocaleAndDomain($localeEntity, $domain);
        } catch (Throwable $e) {
            return new MessageCatalogue($locale);
        }

        $messages = [];

        foreach ($entities as $entity) {
            $messages[$entity->getKey()] = $entity->getFormat();
        }

        $catalogue = new MessageCatalogue($locale);
        $catalogue->add($messages, $domain);

        return $catalogue;
    }

    /**
     * Indicates if translations can be loaded from the database.
     */
    private function isEnabled(): bool
    {
        if ($this->tablesExist === null) {
            $this->tablesExist = $this->localeRepository->tableExists() && $this->messageRepository->tableExists();
        }

        return $this->tablesExist;
    }
}
