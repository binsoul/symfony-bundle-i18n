<?php

declare(strict_types=1);

namespace BinSoul\Symfony\Bundle\I18n\Validator\Constraints;

use Attribute;
use Symfony\Component\Validator\Constraint;

/**
 * Constraint attribute to validate a country code.
 *
 * Validates that the value is a valid ISO 3166-1 alpha-2 country code.
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_METHOD)]
class Country extends Constraint
{
    /**
     * @param string[]|null                               $groups     The validation groups
     * @param string                                      $message    The error message used when the country is invalid
     * @param (callable(string|null): (string|null))|null $normalizer A callable used to normalize the value before validation
     * @param mixed                                       $payload    Domain-specific data attached to the constraint
     */
    public function __construct(
        ?array $groups = null,
        public string $message = 'The country "{{ country }}" is not supported.',
        public $normalizer = null,
        public mixed $payload = null,
    ) {
        parent::__construct(null, $groups, $payload);
    }

    public function validatedBy(): string
    {
        return CountryValidator::class;
    }
}
