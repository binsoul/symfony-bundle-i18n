<?php

declare(strict_types=1);

namespace BinSoul\Symfony\Bundle\I18n\Formatter;

use BinSoul\Common\I18n\DefaultListFormatter;
use BinSoul\Common\I18n\DefaultLocale;
use BinSoul\Common\I18n\ListFormatter as CommonListFormatter;
use BinSoul\Common\I18n\Locale;

/**
 * Formats lists using the {@see DefaultListFormatter} class.
 */
class ListFormatter implements CommonListFormatter
{
    private readonly Locale $locale;

    private readonly DefaultListFormatter $formatter;

    /**
     * Constructs an instance of this class.
     */
    public function __construct(?Locale $locale = null)
    {
        $this->locale = $locale ?? DefaultLocale::fromString(\Locale::getDefault());
        $this->formatter = new DefaultListFormatter($this->locale);
    }

    public function format(array $values): string
    {
        return $this->formatter->format($values);
    }

    public function formatConjunction(array $values): string
    {
        return $this->formatter->formatConjunction($values);
    }

    public function formatDisjunction(array $values): string
    {
        return $this->formatter->formatDisjunction($values);
    }

    public function withLocale(Locale $locale): CommonListFormatter
    {
        if ($locale->getCode() === $this->locale->getCode()) {
            return $this;
        }

        return new self($locale);
    }
}
