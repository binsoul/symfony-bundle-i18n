<?php

declare(strict_types=1);

namespace BinSoul\Symfony\Bundle\I18n\Formatter;

use BinSoul\Common\I18n\Address;
use BinSoul\Common\I18n\AddressFormatter as CommonAddressFormatter;
use BinSoul\Common\I18n\DefaultLocale;
use BinSoul\Common\I18n\Intl\IntlAddressFormatter;
use BinSoul\Common\I18n\Locale;

/**
 * Formats addresses using the {@see IntlAddressFormatter} class.
 */
class AddressFormatter implements CommonAddressFormatter
{
    private readonly Locale $locale;

    private readonly IntlAddressFormatter $formatter;

    /**
     * Constructs an instance of this class.
     */
    public function __construct(?Locale $locale = null)
    {
        $this->locale = $locale ?? DefaultLocale::fromString(\Locale::getDefault());
        $this->formatter = new IntlAddressFormatter($this->locale);
    }

    public function format(Address $address): string
    {
        return $this->formatter->format($address);
    }

    public function generateUsageTemplate(string $countryCode): Address
    {
        return $this->formatter->generateUsageTemplate($countryCode);
    }

    public function generateLabelTemplate(string $countryCode): Address
    {
        return $this->formatter->generateLabelTemplate($countryCode);
    }

    public function generateRegexTemplate(string $countryCode): Address
    {
        return $this->formatter->generateRegexTemplate($countryCode);
    }

    public function generateLayoutTemplate(string $countryCode): Address
    {
        return $this->formatter->generateLayoutTemplate($countryCode);
    }

    public function withLocale(Locale $locale): CommonAddressFormatter
    {
        if ($locale->getCode() === $this->locale->getCode()) {
            return $this;
        }

        return new self($locale);
    }
}
