<?php

declare(strict_types=1);

namespace BinSoul\Symfony\Bundle\I18n\Validator\Constraints;

use BinSoul\Common\I18n\Data\StateData;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Validates states/provinces based on country-specific templates and StateData.
 */
final class StateValidator extends ConstraintValidator
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
     * @param State $constraint
     */
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (! $constraint instanceof State) {
            throw new UnexpectedTypeException($constraint, State::class);
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

        if ($stringValue === null) {
            if ($this->helper->isRequired($constraint, $usageValue)) {
                $this->context->buildViolation($constraint->messageNotBlank)->addViolation();
            }

            return;
        }

        // Validate state against StateData if the country has states
        $valid = true;

        if (StateData::has($country)) {
            $valid = false;
            // Try name/code mapping first
            $code = StateData::buildCode($country, $stringValue);

            if ($code !== null) {
                $valid = true;
            } elseif (str_contains($stringValue, '-')) {
                // Full code path (CC-CODE)
                $valid = StateData::isValidCode($stringValue);
            }
        }

        if (! $valid) {
            $this->context->buildViolation($constraint->messageInvalidState)->addViolation();

            return;
        }

        // Optional regex as additional format constraint
        if (! $this->helper->applyRegex($stringValue, $regexValue)) {
            $this->context->buildViolation($constraint->messageRegex)->addViolation();
        }
    }
}
