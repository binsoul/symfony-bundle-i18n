<?php

declare(strict_types=1);

namespace BinSoul\Symfony\Bundle\I18n\Validator\Constraints;

use Attribute;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Constraint attribute to validate State/Province ("state").
 *
 * In addition to usage/regex logic, if the country defines states,
 * it is always validated against known states from {@see \BinSoul\Common\I18n\Data\StateData}.
 * Accepted values include names (e.g., "California") and codes (e.g., "CA" or "US-CA").
 *
 * Error messages remain neutral; label templates are not used.
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_METHOD)]
class State extends AbstractAddressFieldConstraint
{
    public static string $addressField = 'state';

    /**
     * Constructs a new State constraint.
     *
     * @param string[]|null                                                    $groups                     The validation groups
     * @param string|null                                                      $countryField               The name of the property containing the country code
     * @param string|null                                                      $countryCodeLiteral         A fixed country code to use
     * @param (callable(object|null, ExecutionContextInterface): ?string)|null $countryResolver            A custom resolver for the country code
     * @param bool|null                                                        $forceOptional              Whether to treat the field as optional, regardless of the template
     * @param bool                                                             $forceRequired              Whether to always require the field, regardless of the template
     * @param string                                                           $messageNotBlank            The error message used when a required field is blank
     * @param string                                                           $messageRegex               The error message used when a value does not match the regex pattern
     * @param string                                                           $messageInvalidState        The error message used when a state is invalid
     * @param string                                                           $messageCountryNotSupported The error message used when the country cannot be resolved
     * @param (callable(string|null): (string|null))|null                      $normalizer                 A callable used to normalize the value before validation
     * @param mixed                                                            $payload                    Domain-specific data attached to the constraint
     */
    public function __construct(
        ?array $groups = null,
        ?string $countryField = null,
        ?string $countryCodeLiteral = null,
        ?callable $countryResolver = null,
        ?bool $forceOptional = null,
        bool $forceRequired = false,
        string $messageNotBlank = 'This value should not be blank.',
        string $messageRegex = 'This value is not valid.',
        public string $messageInvalidState = 'This value is not a valid state.',
        string $messageCountryNotSupported = 'The country "{{ country }}" is not supported.',
        ?callable $normalizer = null,
        mixed $payload = null,
    ) {
        parent::__construct(
            $groups,
            $countryField,
            $countryCodeLiteral,
            $countryResolver,
            $forceOptional,
            $forceRequired,
            $messageNotBlank,
            $messageRegex,
            $messageCountryNotSupported,
            $normalizer,
            $payload,
        );
    }

    public function validatedBy(): string
    {
        return StateValidator::class;
    }
}
