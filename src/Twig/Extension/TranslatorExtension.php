<?php

declare(strict_types=1);

namespace BinSoul\Symfony\Bundle\I18n\Twig\Extension;

use BinSoul\Common\I18n\DefaultLocale;
use BinSoul\Common\I18n\DefaultMessage;
use BinSoul\Common\I18n\Locale;
use BinSoul\Common\I18n\Message;
use BinSoul\Common\I18n\PluralizedMessage;
use BinSoul\Common\I18n\TranslatedMessage;
use BinSoul\Common\I18n\Translator as CommonTranslator;
use BinSoul\Symfony\Bundle\I18n\I18nManager;
use BinSoul\Symfony\Bundle\I18n\Translation\Translator;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Provides integration of the {@see Translator} with Twig.
 */
class TranslatorExtension extends AbstractExtension
{
    private I18nManager $i18nManager;

    /**
     * Constructs an instance of this class.
     */
    public function __construct(I18nManager $i18nManager)
    {
        $this->i18nManager = $i18nManager;
    }

    /**
     * @return TwigFilter[]
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter(
                'translate',
                function (string|Message $key, array $parameters = [], ?string $domain = null, Locale|string|null $locale = null): TranslatedMessage {
                    return $this->translate($key, $parameters, $domain, $locale);
                }
            ),
            new TwigFilter(
                'pluralize',
                function (string|Message $key, float|int $quantity, ?string $domain = null, Locale|string|null $locale = null): PluralizedMessage {
                    return $this->pluralize($key, $quantity, $domain, $locale);
                }
            ),
            new TwigFilter(
                'inDomain',
                function (string|Message $key, ?string $domain = null): Message {
                    return $this->inDomain($key, $domain);
                }
            ),
        ];
    }

    /**
     * Translates the key.
     *
     * @param string|Message|PluralizedMessage $key        The message key
     * @param array                            $parameters An array of parameters for the message
     * @param string|null                      $domain     The domain for the message or null to use the default
     * @param Locale|string|null               $locale     The locale for the message or null to use the default
     */
    public function translate(string|Message|PluralizedMessage $key, array $parameters = [], ?string $domain = null, Locale|string|null $locale = null): TranslatedMessage
    {
        return $this->getTranslator($locale)->translate($key, $parameters, $domain);
    }

    /**
     * Pluralizes the key.
     *
     * @param string|Message     $key      The message key
     * @param float|int          $quantity The quantity for the message
     * @param string|null        $domain   The domain for the message or null to use the default
     * @param Locale|string|null $locale   The locale for the message or null to use the default
     */
    public function pluralize(string|Message $key, float|int $quantity, ?string $domain = null, Locale|string|null $locale = null): PluralizedMessage
    {
        return $this->getTranslator($locale)->pluralize($key, $quantity, $domain);
    }

    /**
     * Sets the domain for a key.
     *
     * @param string|Message $key    The message key
     * @param string|null    $domain The domain for the message or null to use the default
     */
    public function inDomain(string|Message $key, ?string $domain = null): Message
    {
        if ($key instanceof Message) {
            return $key->withDomain($domain);
        }

        return new DefaultMessage($key, $domain);
    }

    /**
     * Returns a translator for the given locale.
     */
    private function getTranslator(Locale|string|null $locale): CommonTranslator
    {
        $translator = $this->i18nManager->getEnvironment()->getTranslator();

        if ($locale === null) {
            return $translator;
        }

        if (! ($locale instanceof Locale)) {
            $locale = DefaultLocale::fromString($locale);
        }

        return $translator->withLocale($locale);
    }
}
