<?php

declare(strict_types=1);

namespace BinSoul\Symfony\Bundle\I18n\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Base constraint for country-aware address field validation attributes.
 *
 * These constraint attributes mirror the logic from the AddressFormatter:
 * - Hidden fields (Usage-Template = null) are ignored.
 * - Required fields (Usage-Template = "required") trigger a NotBlank validation,
 *   unless overridden by `forceOptional` or enforced by `forceRequired`.
 * - Format validation is performed using a Regex-Template (positive matching).
 *
 * The associated address field is defined in the child class via the static property
 * `static::$addressField` (e.g., "postalCode").
 *
 * This constraint is only usable as a PHP 8 attribute and supports property and method targets.
 */
abstract class AbstractAddressFieldConstraint extends Constraint
{
    /**
     * @var string Name of the associated address field.
     */
    public static string $addressField = '';

    /**
     * Constructs an instance of this class.
     *
     * @param string[]|null                                                    $groups                     The validation groups
     * @param string|null                                                      $countryField               The name of the property containing the country code
     * @param string|null                                                      $countryCodeLiteral         A fixed country code to use
     * @param (callable(object|null, ExecutionContextInterface): ?string)|null $countryResolver            A custom resolver for the country code
     * @param bool|null                                                        $forceOptional              Whether to treat the field as optional, regardless of the template
     * @param bool                                                             $forceRequired              Whether to always require the field, regardless of the template
     * @param string                                                           $messageNotBlank            The error message used when a required field is blank
     * @param string                                                           $messageRegex               The error message used when a value does not match the regex pattern
     * @param string                                                           $messageCountryNotSupported The error message used when the country cannot be resolved
     * @param (callable(string|null): (string|null))|null                      $normalizer                 A callable used to normalize the value before validation
     * @param mixed                                                            $payload                    Domain-specific data attached to the constraint
     */
    public function __construct(
        ?array $groups = null,
        public ?string $countryField = null,
        public ?string $countryCodeLiteral = null,
        /**
         * @var (callable(object|null, ExecutionContextInterface): ?string)|null
         */
        public $countryResolver = null,
        public ?bool $forceOptional = null,
        public bool $forceRequired = false,
        public string $messageNotBlank = 'This value should not be blank.',
        public string $messageRegex = 'This value is not valid.',
        public string $messageCountryNotSupported = 'The country "{{ country }}" is not supported.',
        /**
         * @var (callable(string|null): (string|null))|null
         */
        public $normalizer = null,
        public mixed $payload = null,
    ) {
        parent::__construct([], $groups, $payload);
    }

    public function validatedBy(): string
    {
        return AddressFieldValidator::class;
    }
}
