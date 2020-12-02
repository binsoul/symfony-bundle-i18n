<?php

declare(strict_types=1);

namespace BinSoul\Symfony\Bundle\I18n\Translation;

use BinSoul\Common\I18n\DefaultLocale;
use BinSoul\Common\I18n\DefaultPluralizedMessage;
use BinSoul\Common\I18n\DefaultTranslatedMessage;
use BinSoul\Common\I18n\Locale;
use BinSoul\Common\I18n\Message;
use BinSoul\Common\I18n\PluralizedMessage;
use BinSoul\Common\I18n\TranslatedMessage;
use BinSoul\Common\I18n\Translator as CommonTranslator;
use InvalidArgumentException;
use Symfony\Component\Translation\TranslatorBagInterface;
use Symfony\Contracts\Translation\LocaleAwareInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Implements the {@see \BinSoul\Common\I18n\Translator Translator} interface using the Symfony translation component.
 */
class Translator implements CommonTranslator
{
    /**
     * @var Locale
     */
    private $locale;

    /**
     * @var TranslatorInterface|TranslatorBagInterface|LocaleAwareInterface
     */
    private $translator;

    /**
     * Constructs an instance of this class.
     *
     * @param TranslatorInterface|TranslatorBagInterface|LocaleAwareInterface $translator
     */
    public function __construct($translator, ?Locale $locale = null)
    {
        if (! $translator instanceof TranslatorInterface || ! $translator instanceof TranslatorBagInterface || ! $translator instanceof LocaleAwareInterface) {
            throw new InvalidArgumentException(sprintf('The Translator "%s" must implement TranslatorInterface, TranslatorBagInterface and LocaleAwareInterface.', \get_class($translator)));
        }

        $this->translator = $translator;
        $this->locale = $locale ?? DefaultLocale::fromString(\Locale::getDefault());
    }

    public function translate($key, array $parameters = [], ?string $domain = null): TranslatedMessage
    {
        /** @var TranslatorBagInterface $translator */
        $translator = $this->translator;
        $catalogue = $translator->getCatalogue($this->locale->getCode('_'));

        if ($key instanceof Message) {
            $targetDomain = ($domain ?? $key->getDomain()) ?? 'messages';
            $parameters = array_merge($key->getParameters(), $parameters);

            if ($key instanceof PluralizedMessage) {
                $parameters['%count%'] = $key->getQuantity();
            }

            /** @var TranslatorInterface $translator */
            $translator = $this->translator;
            $translation = $translator->trans($key->getKey(), $parameters, $targetDomain, $this->locale->getCode('_'));

            return new DefaultTranslatedMessage(
                $key->getKey(),
                $catalogue->get($key->getKey(), $targetDomain),
                $translation,
                $this->locale,
                array_merge($key->getParameters(), $parameters),
                $domain ?? $key->getDomain(),
                $key instanceof PluralizedMessage ? $key->getQuantity() : null
            );
        }

        $targetDomain = $domain ?? 'messages';

        /** @var TranslatorInterface $translator */
        $translator = $this->translator;
        $translation = $translator->trans($key, $parameters, $targetDomain, $this->locale->getCode('_'));

        return new DefaultTranslatedMessage(
            $key,
            $catalogue->get($key, $targetDomain),
            $translation,
            $this->locale,
            $parameters,
            $domain
        );
    }

    public function pluralize($key, $quantity, ?string $domain = null): PluralizedMessage
    {
        /** @var TranslatorBagInterface $translator */
        $translator = $this->translator;
        $catalogue = $translator->getCatalogue($this->locale->getCode('_'));

        if ($key instanceof Message) {
            $targetDomain = ($domain ?? $key->getDomain()) ?? 'messages';

            return new DefaultPluralizedMessage(
                $key->getKey(),
                $catalogue->get($key->getKey(), $targetDomain),
                $quantity,
                $key->getParameters(),
                $domain ?? $key->getDomain()
            );
        }

        $targetDomain = $domain ?? 'messages';

        return new DefaultPluralizedMessage($key, $catalogue->get($key, $targetDomain), $quantity, [], $domain);
    }

    public function withLocale(Locale $locale): CommonTranslator
    {
        if ($locale->getCode() === $this->locale->getCode()) {
            return $this;
        }

        return new self($this->translator, $locale);
    }
}
