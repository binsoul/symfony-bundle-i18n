<?php

declare(strict_types=1);

namespace BinSoul\Symfony\Bundle\I18n\Formatter;

use BinSoul\Common\I18n\DefaultAddressFormatter;
use BinSoul\Common\I18n\DefaultLocale;
use BinSoul\Common\I18n\Locale;
use Symfony\Component\Intl\Countries;
use Symfony\Component\Intl\Exception\MissingResourceException;

class AddressFormatter extends DefaultAddressFormatter
{
    public function __construct(?Locale $locale = null)
    {
        parent::__construct($locale ?? DefaultLocale::fromString(\Locale::getDefault()));
    }

    protected function isoCodeToName(string $isoCode): string
    {
        try {
            $result = Countries::getName($isoCode, $this->locale->getCode('_'));
        } catch (MissingResourceException $e) {
            $result = $isoCode;
        }

        return $result;
    }
}
