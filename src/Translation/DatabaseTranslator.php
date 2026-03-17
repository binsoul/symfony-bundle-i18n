<?php

declare(strict_types=1);

namespace BinSoul\Symfony\Bundle\I18n\Translation;

use BadMethodCallException;
use BinSoul\Common\I18n\DefaultLocale;
use BinSoul\Symfony\Bundle\I18n\Entity\LocaleEntity;
use BinSoul\Symfony\Bundle\I18n\Repository\LocaleRepository;
use BinSoul\Symfony\Bundle\I18n\Repository\MessageRepository;
use InvalidArgumentException;
use Symfony\Component\Translation\Exception\NotFoundResourceException;
use Symfony\Component\Translation\Formatter\IntlFormatterInterface;
use Symfony\Component\Translation\Formatter\MessageFormatter;
use Symfony\Component\Translation\Formatter\MessageFormatterInterface;
use Symfony\Component\Translation\MessageCatalogue;
use Symfony\Component\Translation\MessageCatalogueInterface;
use Symfony\Component\Translation\TranslatorBagInterface;
use Symfony\Contracts\Translation\LocaleAwareInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Throwable;

/**
 * Replaces the original Symfony FrameworkBundle translator.
 */
class DatabaseTranslator implements TranslatorInterface, TranslatorBagInterface, LocaleAwareInterface
{
    private readonly MessageFormatterInterface $messageFormatter;

    /**
     * @var array<string, array<string, bool>>
     */
    private array $loadedCatalogues = [];

    /**
     * @var array<string, MessageCatalogueInterface>
     */
    private array $databaseCatalogues = [];

    /**
     * @var array<string, LocaleEntity|null>
     */
    private array $cachedLocale = [];

    public function __construct(
        private readonly TranslatorInterface $defaultTranslator,
        private readonly MessageRepository $messageRepository,
        private readonly LocaleRepository $localeRepository,
        ?MessageFormatterInterface $formatter = null,
    ) {
        $this->messageFormatter = $formatter ?? new MessageFormatter();
    }

    /**
     * Passes through all unknown calls onto the default translator object.
     *
     * @param mixed[] $args
     */
    public function __call(string $method, array $args): mixed
    {
        $callable = [$this->defaultTranslator, $method];

        if (is_callable($callable)) {
            return call_user_func_array($callable, $args);
        }

        throw new BadMethodCallException(sprintf('Method "%s" does not exist on "%s".', $method, $this->defaultTranslator::class));
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function trans(?string $id, array $parameters = [], ?string $domain = null, ?string $locale = null): string
    {
        $idString = (string) $id;

        if ($idString === '') {
            return '';
        }

        if ($domain === null) {
            $domain = 'messages';
        }

        $catalogue = $this->load($locale ?? $this->getLocale(), $domain);
        $locale = $catalogue->getLocale();

        while (! $catalogue->defines($idString, $domain)) {
            $fallbackCatalogue = $catalogue->getFallbackCatalogue();

            if ($fallbackCatalogue === null) {
                break;
            }

            $catalogue = $fallbackCatalogue;
            $locale = $catalogue->getLocale();
        }

        if (! $catalogue->defines($idString, $domain)) {
            return $this->defaultTranslator->trans($idString, $parameters, $domain, $locale);
        }

        if ($this->messageFormatter instanceof IntlFormatterInterface) {
            return $this->messageFormatter->formatIntl($catalogue->get($idString, $domain), $locale, $parameters);
        }

        return $this->messageFormatter->format($catalogue->get($idString, $domain), $locale, $parameters);
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

    public function getLocale(): string
    {
        return $this->defaultTranslator->getLocale();
    }

    public function setLocale(string $locale): void
    {
        if (! $this->defaultTranslator instanceof LocaleAwareInterface) {
            throw new InvalidArgumentException('The default translator must implements LocaleAwareInterface.');
        }

        $this->defaultTranslator->setLocale($locale);
    }

    public function getCatalogue(?string $locale = null): MessageCatalogueInterface
    {
        if (! $this->defaultTranslator instanceof TranslatorBagInterface) {
            throw new InvalidArgumentException('The default translator must implements TranslatorBagInterface.');
        }

        return $this->defaultTranslator->getCatalogue($locale);
    }

    public function getCatalogues(): array
    {
        if (! $this->defaultTranslator instanceof TranslatorBagInterface) {
            throw new InvalidArgumentException('The default translator must implements TranslatorBagInterface.');
        }

        return $this->defaultTranslator->getCatalogues();
    }
}
