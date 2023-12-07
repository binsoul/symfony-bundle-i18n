<?php

declare(strict_types=1);

namespace BinSoul\Symfony\Bundle\I18n\Transliterator;

use BinSoul\Common\I18n\DefaultLocale;
use BinSoul\Common\I18n\Intl\IntlSlugGenerator;
use BinSoul\Common\I18n\Locale;
use BinSoul\Common\I18n\SlugGenerator as CommonSlugGenerator;

/**
 * Generates slugs using the {@see IntlSlugGenerator} class.
 */
class SlugGenerator implements CommonSlugGenerator
{
    private readonly Locale $locale;

    private readonly IntlSlugGenerator $generator;

    /**
     * Constructs an instance of this class.
     */
    public function __construct(?Locale $locale = null)
    {
        $this->locale = $locale ?? DefaultLocale::fromString(\Locale::getDefault());
        $this->generator = new IntlSlugGenerator($this->locale);
    }

    public function transliterate(string $text, array $rules = []): string
    {
        return $this->generator->transliterate($text, $rules);
    }

    public function withLocale(Locale $locale): CommonSlugGenerator
    {
        if ($locale->getCode() === $this->locale->getCode()) {
            return $this;
        }

        return new self($locale);
    }
}
