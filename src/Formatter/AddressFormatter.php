<?php

declare(strict_types=1);

namespace BinSoul\Symfony\Bundle\I18n\Formatter;

use BinSoul\Common\I18n\DefaultAddressFormatter;
use Symfony\Component\Intl\Countries;
use Symfony\Component\Intl\Exception\MissingResourceException;

class AddressFormatter extends DefaultAddressFormatter
{
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
