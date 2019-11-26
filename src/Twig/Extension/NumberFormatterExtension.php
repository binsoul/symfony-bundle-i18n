<?php

declare(strict_types=1);

namespace BinSoul\Symfony\Bundle\I18n\Twig\Extension;

use BinSoul\Common\I18n\DefaultLocale;
use BinSoul\Common\I18n\Intl\IntlNumberFormatter;
use BinSoul\Common\I18n\Locale;
use BinSoul\Common\I18n\NumberFormatter;
use BinSoul\Symfony\Bundle\I18n\Entity\CurrencyEntity;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Provides integration of the {@see IntlNumberFormatter} with Twig.
 */
class NumberFormatterExtension extends AbstractExtension
{
    /**
     * @var IntlNumberFormatter
     */
    private $formatter;

    /**
     * Constructs an instance of this class.
     */
    public function __construct(IntlNumberFormatter $numberFormatter)
    {
        $this->formatter = $numberFormatter;
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
     * @param float|int|string      $value    The number
     * @param CurrencyEntity|string $currency Currency entity or ISO3 code of the currency
     * @param Locale|string|null    $locale   The locale or null to use the default
     */
    public function formatCurrency($value, $currency, $locale = null): string
    {
        return $this->getFormatter($locale)->formatCurrency((float) $value, $currency instanceof CurrencyEntity ? $currency->getIso3() : (string) $currency);
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
    private function getFormatter($locale): NumberFormatter
    {
        if ($locale === null) {
            return $this->formatter;
        }

        if (!($locale instanceof Locale)) {
            $locale = DefaultLocale::fromString((string) $locale);
        }

        return $this->formatter->withLocale($locale);
    }
}
