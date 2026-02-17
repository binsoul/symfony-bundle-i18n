<?php

declare(strict_types=1);

namespace BinSoul\Symfony\Bundle\I18n\Validator\Constraints;

use BinSoul\Common\I18n\Address;
use Exception;
use Symfony\Component\Intl\Countries;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Helper for country-aware address field validation.
 */
class AddressValidationHelper
{
    /**
     * @var array<string, Address> Cache for usage templates
     */
    private array $usageCache = [];

    /**
     * @var array<string, Address> Cache for regex templates
     */
    private array $regexCache = [];

    /**
     * Constructs an instance of this class.
     *
     * @param AddressValidationConfig $config The address validation configuration
     */
    public function __construct(
        private readonly AddressValidationConfig $config,
    ) {
    }

    /**
     * Resolves the country code using the defined precedence.
     * Precedence: countryCodeLiteral -> countryResolver -> countryField -> defaultCountry.
     *
     * @param AbstractAddressFieldConstraint $constraint The constraint being validated
     * @param ExecutionContextInterface      $context    The validation context
     *
     * @return array{?string, ?string} A tuple containing [resolved country code, attempted country code]
     */
    public function resolveCountryCode(AbstractAddressFieldConstraint $constraint, ExecutionContextInterface $context): array
    {
        $object = $context->getObject();
        $attempted = null;

        // 1. Country code literal
        if ($constraint->countryCodeLiteral !== null && trim($constraint->countryCodeLiteral) !== '') {
            $attempted = strtoupper(trim($constraint->countryCodeLiteral));
        }

        // 2. Country resolver
        if ($attempted === null && $constraint->countryResolver !== null && is_callable($constraint->countryResolver)) {
            $resolverCode = ($constraint->countryResolver)($object, $context);

            if (is_string($resolverCode) && trim($resolverCode) !== '') {
                $attempted = strtoupper(trim($resolverCode));
            }
        }

        // 3. Country field
        if ($attempted === null && $constraint->countryField !== null && is_object($object)) {
            $accessor = PropertyAccess::createPropertyAccessor();

            try {
                $fieldValue = $accessor->getValue($object, $constraint->countryField);
            } catch (Exception) {
                $fieldValue = null;
            }

            if (is_object($fieldValue) && method_exists($fieldValue, 'getIso2')) {
                $fieldValue = $fieldValue->getIso2();
            }

            if (is_string($fieldValue) && trim($fieldValue) !== '') {
                $attempted = strtoupper(trim($fieldValue));
            }
        }

        // 4. Default country
        if ($attempted === null && $this->config->defaultCountry !== null) {
            $attempted = strtoupper(trim($this->config->defaultCountry));
        }

        if ($attempted !== null && Countries::exists($attempted)) {
            return [$attempted, $attempted];
        }

        return [null, $attempted];
    }

    /**
     * Returns a tuple [usage, regex] for the given field and country.
     *
     * @param string $country The ISO 3166-1 alpha-2 country code
     * @param string $field   The name of the address field
     *
     * @return array{?string, ?string} A tuple containing usage (null, "required", "optional") and regex pattern
     */
    public function getTemplates(string $country, string $field): array
    {
        if (! isset($this->usageCache[$country])) {
            $this->usageCache[$country] = $this->config->addressFormatter->generateUsageTemplate($country);
        }

        if (! isset($this->regexCache[$country])) {
            $this->regexCache[$country] = $this->config->addressFormatter->generateRegexTemplate($country);
        }

        $usage = $this->usageCache[$country];
        $regex = $this->regexCache[$country];

        $getter = 'get' . ucfirst($field);

        /** @var ?string $usageValue */
        $usageValue = is_callable([$usage, $getter]) ? $usage->{$getter}() : null;
        /** @var ?string $regexValue */
        $regexValue = is_callable([$regex, $getter]) ? $regex->{$getter}() : null;

        return [$usageValue, $regexValue];
    }

    /**
     * Returns whether the field is considered required for the given constraint and usage value.
     *
     * @param AbstractAddressFieldConstraint $constraint The constraint being validated
     * @param string|null                    $usageValue The usage template value ("required", "optional" or null)
     *
     * @return bool True if the field is required
     */
    public function isRequired(AbstractAddressFieldConstraint $constraint, ?string $usageValue): bool
    {
        $forceOptional = $constraint->forceOptional ?? false;

        if ($constraint->forceRequired) {
            return true;
        }

        return ! $forceOptional && $usageValue === 'required';
    }

    /**
     * Normalizes the given value to a string or null and applies an optional normalizer.
     *
     * @param mixed                                       $value      The value to normalize
     * @param (callable(string|null): (string|null))|null $normalizer An optional callable used to normalize the value
     *
     * @return string|null The normalized string value or null
     *
     * @throws UnexpectedTypeException If the value cannot be converted to a string
     */
    public function normalizeValue(mixed $value, ?callable $normalizer): ?string
    {
        if ($value !== null && ! is_scalar($value) && ! (is_object($value) && method_exists($value, '__toString'))) {
            throw new UnexpectedTypeException($value, 'string');
        }

        $stringValue = $value === null ? null : (string) $value;

        if ($normalizer !== null) {
            $stringValue = $normalizer($stringValue);
        }

        return $stringValue;
    }

    /**
     * Applies the given regex pattern to the value.
     *
     * @param string|null $value The value to validate
     * @param string|null $regex The regex pattern (without delimiters)
     *
     * @return bool True if the value matches the regex or if no regex is provided
     */
    public function applyRegex(?string $value, ?string $regex): bool
    {
        if ($regex === null || $regex === '') {
            return true;
        }

        $pattern = '/' . $regex . '/';

        return @preg_match($pattern, (string) $value) === 1;
    }
}
