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
                'formatDecimal',
                function (float|int|string $value, ?int $decimals = null, Locale|string|null $locale = null): string {
                    return $this->formatDecimal($value, $decimals, $locale);
                }
            ),
            new TwigFilter(
                'formatPercent',
                function (float|int|string $value, ?int $decimals = null, Locale|string|null $locale = null): string {
                    return $this->formatPercent($value, $decimals, $locale);
                }
            ),
            new TwigFilter(
                'formatCurrency',
                function (float|int|string $value, Currency|string $currency, Locale|string|null $locale = null): string {
                    return $this->formatCurrency($value, $currency, $locale);
                }
            ),
            new TwigFilter(
                'formatCurrencyNumber',
                function (float|int|string $value, Locale|string|null $locale = null): string {
                    return $this->formatCurrencyNumber($value, $locale);
                }
            ),
        ];
    }

    /**
     * Formats a decimal number.
     *
     * @param float|int|string   $value    The number
     * @param int|null           $decimals Maximum number of fractional digits
     * @param Locale|string|null $locale   The locale or null to use the default
     */
    public function formatDecimal(float|int|string $value, ?int $decimals = null, Locale|string|null $locale = null): string
    {
        return $this->getFormatter($locale)->formatDecimal((float) $value, $decimals);
    }

    /**
     * Formats a decimal number as a percent value.
     *
     * @param float|int|string   $value    The number
     * @param int|null           $decimals Maximum number of fractional digits
     * @param Locale|string|null $locale   The locale or null to use the default
     */
    public function formatPercent(float|int|string $value, ?int $decimals = null, Locale|string|null $locale = null): string
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
    public function formatCurrency(float|int|string $value, Currency|string $currency, Locale|string|null $locale = null): string
    {
        return $this->getFormatter($locale)->formatCurrency((float) $value, $currency instanceof Currency ? $currency->getIso3() : $currency);
    }

    /**
     *  Formats a decimal number as a currency value without the currency symbol.
     *
     * @param float|int|string   $value  The number
     * @param Locale|string|null $locale The locale or null to use the default
     */
    public function formatCurrencyNumber(float|int|string $value, Locale|string|null $locale = null): string
    {
        return $this->getFormatter($locale)->formatCurrency((float) $value);
    }

    /**
     * Returns a formatter for the given locale.
     */
    private function getFormatter(Locale|string|null $locale): CommonNumberFormatter
    {
        $formatter = $this->i18nManager->getEnvironment()->getNumberFormatter();

        if ($locale === null) {
            return $formatter;
        }

        if (! ($locale instanceof Locale)) {
            $locale = DefaultLocale::fromString($locale);
        }

        return $formatter->withLocale($locale);
    }
}
