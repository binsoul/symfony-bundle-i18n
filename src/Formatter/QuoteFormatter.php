<?php

declare(strict_types=1);

namespace BinSoul\Symfony\Bundle\I18n\Formatter;

use BinSoul\Common\I18n\DefaultLocale;
use BinSoul\Common\I18n\DefaultQuoteFormatter;
use BinSoul\Common\I18n\Locale;
use BinSoul\Common\I18n\QuoteFormatter as CommonQuoteFormatter;

/**
 * Formats quotes using the {@see DefaultQuoteFormatter} class.
 */
readonly class QuoteFormatter implements CommonQuoteFormatter
{
    private Locale $locale;

    private DefaultQuoteFormatter $formatter;

    /**
     * Constructs an instance of this class.
     */
    public function __construct(?Locale $locale = null)
    {
        $this->locale = $locale ?? DefaultLocale::fromString(\Locale::getDefault());
        $this->formatter = new DefaultQuoteFormatter($this->locale);
    }

    public function primary($value)
    {
        return $this->formatter->primary($value);
    }

    public function secondary($value)
    {
        return $this->formatter->secondary($value);
    }

    public function withLocale(Locale $locale): CommonQuoteFormatter
    {
        if ($locale->getCode() === $this->locale->getCode()) {
            return $this;
        }

        return new self($locale);
    }
}
