<?php

declare(strict_types=1);

namespace BinSoul\Symfony\Bundle\I18n\Service;

use BinSoul\Common\I18n\AddressFormatter as CommonAddressFormatter;
use BinSoul\Common\I18n\DateTimeFormatter as CommonDateTimeFormatter;
use BinSoul\Common\I18n\Locale;
use BinSoul\Common\I18n\NumberFormatter as CommonNumberFormatter;
use BinSoul\Common\I18n\SlugGenerator as CommonSlugGenerator;
use BinSoul\Common\I18n\Translator as CommonTranslator;
use BinSoul\Symfony\Bundle\I18n\Formatter\AddressFormatter;
use BinSoul\Symfony\Bundle\I18n\Formatter\DateTimeFormatter;
use BinSoul\Symfony\Bundle\I18n\Formatter\ListFormatter;
use BinSoul\Symfony\Bundle\I18n\Formatter\NumberFormatter;
use BinSoul\Symfony\Bundle\I18n\Formatter\QuoteFormatter;
use BinSoul\Symfony\Bundle\I18n\I18nEnvironment;
use BinSoul\Symfony\Bundle\I18n\Translation\Translator;
use BinSoul\Symfony\Bundle\I18n\Transliterator\SlugGenerator;

class Environment implements I18nEnvironment
{
    private Locale $locale;

    private NumberFormatter $numberFormatter;

    private DateTimeFormatter $dateTimeFormatter;

    private AddressFormatter $addressFormatter;

    private Translator $translator;

    private ?SlugGenerator $slugGenerator = null;

    private ListFormatter $listFormatter;

    private QuoteFormatter $quoteFormatter;

    /**
     * Constructs an instance of this class.
     */
    public function __construct(
        Locale $locale,
        NumberFormatter $numberFormatter,
        DateTimeFormatter $dateTimeFormatter,
        AddressFormatter $addressFormatter,
        Translator $translator,
        ListFormatter $listFormatter,
        QuoteFormatter $quoteFormatter
    ) {
        $this->locale = $locale;
        $this->numberFormatter = $numberFormatter;
        $this->dateTimeFormatter = $dateTimeFormatter;
        $this->addressFormatter = $addressFormatter;
        $this->translator = $translator;
        $this->listFormatter = $listFormatter;
        $this->quoteFormatter = $quoteFormatter;
    }

    public function getLocale(): Locale
    {
        return $this->locale;
    }

    public function getNumberFormatter(): CommonNumberFormatter
    {
        return $this->numberFormatter;
    }

    public function getDateTimeFormatter(): CommonDateTimeFormatter
    {
        return $this->dateTimeFormatter;
    }

    public function getAddressFormatter(): CommonAddressFormatter
    {
        return $this->addressFormatter;
    }

    public function getTranslator(): CommonTranslator
    {
        return $this->translator;
    }

    public function getListFormatter(): ListFormatter
    {
        return $this->listFormatter;
    }

    public function getQuoteFormatter(): QuoteFormatter
    {
        return $this->quoteFormatter;
    }

    public function getSlugGenerator(): CommonSlugGenerator
    {
        if ($this->slugGenerator === null) {
            $this->slugGenerator = new SlugGenerator($this->locale);
        }

        return $this->slugGenerator;
    }
}
