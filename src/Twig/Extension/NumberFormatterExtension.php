<?php

declare(strict_types=1);

namespace BinSoul\Symfony\Bundle\I18n\Twig\Extension;

use BinSoul\Common\I18n\Currency;
use BinSoul\Common\I18n\DefaultLocale;
use BinSoul\Common\I18n\Locale;
use BinSoul\Common\I18n\NumberFormatter as CommonNumberFormatter;
use BinSoul\Symfony\Bundle\I18n\Formatter\NumberFormatter;
use BinSoul\Symfony\Bundle\I18n\I18nManager;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Provides integration of the {@see NumberFormatter} with Twig.
 */
class NumberFormatterExtension extends AbstractExtension
{
    /**
     * @var I18nManager
     */
    private $i18nManager;

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
            new TwigFilter('formatDecimal', [$this, 'formatDecimal']),
            new TwigFilter('formatPercent', [$this, 'formatPercent']),
            new TwigFilter('formatCurrency', [$this, 'formatCurrency']),
            new TwigFilter('formatCurrencyNumber', [$this, 'formatCurrencyNumber']),
        ];
    }

    /**
     * Formats a decimal number.
     *
     * @param float|int|string   $value    The number
     * @param int                $decimals Maximum number of fractional digits
     * @param Locale|string|null $locale   The locale or null to use the default
     */
    public function formatDecimal($value, int $decimals = null, $locale = null): string
    {
        return $this->getFormatter($locale)->formatDecimal((float) $value, $decimals);
    }

    /**
     * Formats a decimal number as a percent value.
     *
     * @param float|int|string   $value    The number
     * @param int                $decimals Maximum number of fractional digits
     * @param Locale|string|null $locale   The locale or null to use the default
     */
    public function formatPercent($value, int $decimals = null, $locale = null): string
    {
        return $this->getFormatter($locale)->formatPercent((float) $value, $decimals);
    }

    /**
     *  Formats a decimal number as a currency value.
     *
     * @param float|int|string   $value    The number
     * @param Currency|string    $currency Currency interface or ISO3 code of the currency
     * @param Locale|string|null $locale   The locale or null to use the default
     */
    public function formatCurrency($value, $currency, $locale = null): string
    {
        return $this->getFormatter($locale)->formatCurrency((float) $value, $currency instanceof Currency ? $currency->getIso3() : (string) $currency);
    }

    /**
     *  Formats a decimal number as a currency value without the currency symbol.
     *
     * @param float|int|string   $value  The number
     * @param Locale|string|null $locale The locale or null to use the default
     */
    public function formatCurrencyNumber($value, $locale = null): string
    {
        return $this->getFormatter($locale)->formatCurrency((float) $value);
    }

    /**
     * Returns a formatter for the given locale.
     *
     * @param Locale|string|null $locale
     */
    private function getFormatter($locale): CommonNumberFormatter
    {
        $formatter = $this->i18nManager->getEnvironment()->getNumberFormatter();
        if ($locale === null) {
            return $formatter;
        }

        if (!($locale instanceof Locale)) {
            $locale = DefaultLocale::fromString((string) $locale);
        }

        return $formatter->withLocale($locale);
    }
}
