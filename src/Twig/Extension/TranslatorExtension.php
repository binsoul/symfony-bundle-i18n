<?php

declare(strict_types=1);

namespace BinSoul\Symfony\Bundle\I18n\Twig\Extension;

use BinSoul\Common\I18n\DefaultLocale;
use BinSoul\Common\I18n\Locale;
use BinSoul\Common\I18n\Message;
use BinSoul\Common\I18n\PluralizedMessage;
use BinSoul\Common\I18n\TranslatedMessage;
use BinSoul\Common\I18n\Translator as CommonTranslator;
use BinSoul\Symfony\Bundle\I18n\Translation\Translator;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Provides integration of the {@see Translator} with Twig.
 */
class TranslatorExtension extends AbstractExtension
{
    /**
     * @var Translator
     */
    private $translator;

    /**
     * Constructs an instance of this class.
     */
    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @return TwigFilter[]
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('translate', [$this, 'translate']),
            new TwigFilter('pluralize', [$this, 'pluralize']),
        ];
    }

    /**
     * Translates the key.
     *
     * @param string|Message|PluralizedMessage $key        The message key
     * @param mixed[]                          $parameters An array of parameters for the message
     * @param string|null                      $domain     The domain for the message or null to use the default
     * @param Locale|string|null               $locale     The locale for the message or null to use the default
     */
    public function translate($key, array $parameters = [], ?string $domain = null, $locale = null): TranslatedMessage
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
    public function pluralize($key, $quantity, ?string $domain = null, $locale = null): PluralizedMessage
    {
        return $this->getTranslator($locale)->pluralize($key, $quantity, $domain);
    }

    /**
     * Returns a translator for the given locale.
     *
     * @param Locale|string|null $locale
     */
    private function getTranslator($locale): CommonTranslator
    {
        if ($locale === null) {
            return $this->translator;
        }

        if (!($locale instanceof Locale)) {
            $locale = DefaultLocale::fromString((string) $locale);
        }

        return $this->translator->withLocale($locale);
    }
}
