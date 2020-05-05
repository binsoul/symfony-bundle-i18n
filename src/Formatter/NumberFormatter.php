<?php

declare(strict_types=1);

namespace BinSoul\Symfony\Bundle\I18n\Formatter;

use BinSoul\Common\I18n\DefaultLocale;
use BinSoul\Common\I18n\Intl\IntlNumberFormatter;
use BinSoul\Common\I18n\Locale;
use BinSoul\Common\I18n\NumberFormatter as CommonNumberFormatter;

/**
 * Formats numbers using the {@see IntlNumberFormatter} class.
 */
class NumberFormatter implements CommonNumberFormatter
{
    /**
     * @var Locale
     */
    private $locale;

    /**
     * @var IntlNumberFormatter
     */
    private $formatter;

    /**
     * Constructs an instance of this class.
     */
    public function __construct(?Locale $locale = null)
    {
        $this->locale = $locale ?? DefaultLocale::fromString(\Locale::getDefault());
        $this->formatter = new IntlNumberFormatter($this->locale);
    }

    public function formatDecimal(float $value, ?int $decimals = null): string
    {
        return $this->formatter->formatDecimal($value, $decimals);
    }

    public function formatPercent(float $value, ?int $decimals = null): string
    {
        return $this->formatter->formatPercent($value, $decimals);
    }

    public function formatCurrency(float $value, string $currencyCode = ''): string
    {
        return $this->formatter->formatCurrency($value, $currencyCode);
    }

    public function withLocale(Locale $locale): CommonNumberFormatter
    {
        if ($locale->getCode() === $this->locale->getCode()) {
            return $this;
        }

        return new self($locale);
    }
}
