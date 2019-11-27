<?php

declare(strict_types=1);

namespace BinSoul\Symfony\Bundle\I18n;

use BinSoul\Common\I18n\AddressFormatter;
use BinSoul\Common\I18n\DateTimeFormatter;
use BinSoul\Common\I18n\Locale;
use BinSoul\Common\I18n\NumberFormatter;
use BinSoul\Common\I18n\Translator;

/**
 * Represent an environment configured for a specific locale.
 */
interface I18nEnvironment
{
    /**
     * Returns the locale for this environment.
     */
    public function getLocale(): Locale;

    /**
     * Returns an object which can format numbers.
     */
    public function getNumberFormatter(): NumberFormatter;

    /**
     * Returns an object which can format dates and times.
     */
    public function getDateTimeFormatter(): DateTimeFormatter;

    /**
     * Returns an object which can format addresses.
     */
    public function getAddressFormatter(): AddressFormatter;

    /**
     * Returns an object which can translate strings.
     */
    public function getTranslator(): Translator;
}
