<?php

declare(strict_types=1);

namespace BinSoul\Symfony\Bundle\I18n\Translation;

use BinSoul\Common\I18n\DefaultLocale;
use BinSoul\Symfony\Bundle\I18n\Entity\LocaleEntity;
use BinSoul\Symfony\Bundle\I18n\Repository\LocaleRepository;
use BinSoul\Symfony\Bundle\I18n\Repository\MessageRepository;
use InvalidArgumentException;
use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Translation\Translator as BaseTranslator;
use Symfony\Component\Translation\Exception\NotFoundResourceException;
use Symfony\Component\Translation\Formatter\IntlFormatterInterface;
use Symfony\Component\Translation\Formatter\MessageFormatterInterface;
use Symfony\Component\Translation\MessageCatalogue;
use Symfony\Component\Translation\MessageCatalogueInterface;
use Throwable;

/**
 * Replaces the original Symfony FrameworkBundle translator.
 */
class DatabaseTranslator extends BaseTranslator
{
    private readonly MessageRepository $messageRepository;

    private readonly LocaleRepository $localeRepository;

    private readonly MessageFormatterInterface $messageFormatter;

    /**
     * @var bool[][]
     */
    private array $loadedCatalogues = [];

    /**
     * @var MessageCatalogueInterface[]
     */
    private array $databaseCatalogues = [];

    /**
     * @var array<string, LocaleEntity|null>
     */
    private array $cachedLocale = [];

    public function __construct(
        ContainerInterface $container,
        MessageFormatterInterface $formatter,
        string $defaultLocale,
        array $loaderIds = [],
        array $options = [],
        array $enabledLocales = [],
        ?MessageRepository $messageRepository = null,
        ?LocaleRepository $localeRepository = null
    ) {
        parent::__construct($container, $formatter, $defaultLocale, $loaderIds, $options, $enabledLocales);

        $this->messageFormatter = $formatter;

        if ($messageRepository === null) {
            throw new InvalidArgumentException('A message repository is required.');
        }

        $this->messageRepository = $messageRepository;

        if ($localeRepository === null) {
            throw new InvalidArgumentException('A locale repository is required.');
        }

        $this->localeRepository = $localeRepository;
    }

    public function trans(?string $id, array $parameters = [], string $domain = null, string $locale = null): string
    {
        if ((string) $id === '') {
            return '';
        }

        if ($domain === null) {
            $domain = 'messages';
        }

        $catalogue = $this->load($locale ?? $this->getLocale(), $domain);
        $locale = $catalogue->getLocale();

        while (! $catalogue->defines($id, $domain)) {
            $fallbackCatalogue = $catalogue->getFallbackCatalogue();

            if ($fallbackCatalogue === null) {
                break;
            }

            $catalogue = $fallbackCatalogue;
            $locale = $catalogue->getLocale();
        }

        if (! $catalogue->defines($id, $domain)) {
            return parent::trans($id, $parameters, $domain, $locale);
        }

        if ($this->messageFormatter instanceof IntlFormatterInterface) {
            return $this->messageFormatter->formatIntl($catalogue->get($id, $domain), $locale, $parameters);
        }

        return $this->messageFormatter->format($catalogue->get($id, $domain), $locale, $parameters);
    }

    public function load(string $locale, string $domain): MessageCatalogueInterface
    {
        if (isset($this->loadedCatalogues[$locale][$domain])) {
            return $this->databaseCatalogues[$locale];
        }

        $localeEntity = null;
        $parsedLocale = DefaultLocale::fromString($locale, '_');

        while (! $parsedLocale->isRoot()) {
            $parsedLocaleCode = $parsedLocale->getCode('-');

            if (! array_key_exists($parsedLocaleCode, $this->cachedLocale)) {
                $this->cachedLocale[$parsedLocaleCode] = $this->localeRepository->findByCode($parsedLocaleCode);
            }

            $localeEntity = $this->cachedLocale[$parsedLocaleCode];

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
        } catch (Throwable) {
            return new MessageCatalogue($locale);
        }

        $messages = [];

        foreach ($entities as $entity) {
            $messages[$entity->getKey()] = $entity->getFormat();
        }

        if (! isset($this->databaseCatalogues[$locale])) {
            $this->databaseCatalogues[$locale] = new MessageCatalogue($locale);
        }

        $this->databaseCatalogues[$locale]->add($messages, $domain);

        if (! isset($this->loadedCatalogues[$locale])) {
            $this->loadedCatalogues[$locale] = [];
        }

        $this->loadedCatalogues[$locale][$domain] = true;

        $parsedLocale = DefaultLocale::fromString($localeEntity->getCode());
        $parsedLocale = $parsedLocale->getParent();

        if (! $parsedLocale->isRoot()) {
            $fallbackCatalogue = $this->load($parsedLocale->getCode('_'), $domain);
            $this->databaseCatalogues[$locale]->addFallbackCatalogue($fallbackCatalogue);
        }

        return $this->databaseCatalogues[$locale];
    }
}
