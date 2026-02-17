<?php

declare(strict_types=1);

namespace BinSoul\Symfony\Bundle\I18n\Validator\Constraints;

use BinSoul\Common\I18n\AddressFormatter;

/**
 * Configuration for country-aware address field validation.
 */
readonly class AddressValidationConfig
{
    /**
     * Constructs an instance of this class.
     *
     * @param AddressFormatter $addressFormatter The address formatter used to generate templates
     * @param string|null      $defaultCountry   The default ISO 3166-1 alpha-2 country code
     */
    public function __construct(
        public AddressFormatter $addressFormatter,
        public ?string $defaultCountry = null,
    ) {
    }
}
