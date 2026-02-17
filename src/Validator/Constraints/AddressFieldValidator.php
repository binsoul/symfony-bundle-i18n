<?php

declare(strict_types=1);

namespace BinSoul\Symfony\Bundle\I18n\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Validates address fields based on country-specific templates.
 */
final class AddressFieldValidator extends ConstraintValidator
{
    /**
     * Constructs an instance of this class.
     *
     * @param AddressValidationHelper $helper The address validation helper
     */
    public function __construct(
        private readonly AddressValidationHelper $helper,
    ) {
    }

    /**
     * @param AbstractAddressFieldConstraint $constraint
     */
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (! $constraint instanceof AbstractAddressFieldConstraint) {
            throw new UnexpectedTypeException($constraint, AbstractAddressFieldConstraint::class);
        }

        $stringValue = $this->helper->normalizeValue($value, $constraint->normalizer);

        [$country, $attempted] = $this->helper->resolveCountryCode($constraint, $this->context);

        if ($country === null) {
            $display = $attempted !== null ? strtoupper($attempted) : 'n/a';

            $this->context->buildViolation($constraint->messageCountryNotSupported)
                ->setParameter('{{ country }}', $display)
                ->addViolation();

            return;
        }

        [$usageValue, $regexValue] = $this->helper->getTemplates($country, $constraint::$addressField);

        if ($usageValue === null) {
            return;
        }

        $isEmpty = $stringValue === null || trim($stringValue) === '';

        if ($this->helper->isRequired($constraint, $usageValue) && $isEmpty) {
            $this->context->buildViolation($constraint->messageNotBlank)->addViolation();

            return;
        }

        // If empty and not required → skip regex
        if ($isEmpty) {
            return;
        }

        if (! $this->helper->applyRegex($stringValue, $regexValue)) {
            $this->context->buildViolation($constraint->messageRegex)->addViolation();
        }
    }
}
