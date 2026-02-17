<?php

declare(strict_types=1);

namespace BinSoul\Symfony\Bundle\I18n\Validator\Constraints;

use Symfony\Component\Intl\Countries;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Validates that a value is a valid ISO 3166-1 alpha-2 country code.
 */
final class CountryValidator extends ConstraintValidator
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
     * @param Country $constraint
     */
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (! $constraint instanceof Country) {
            throw new UnexpectedTypeException($constraint, Country::class);
        }

        $stringValue = $value;

        if (is_object($value) && method_exists($value, 'getIso2')) {
            $stringValue = $value->getIso2();
        }

        $stringValue = $this->helper->normalizeValue($stringValue, $constraint->normalizer);

        if ($stringValue === null) {
            return;
        }

        if (! Countries::exists(strtoupper(trim($stringValue)))) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ country }}', $stringValue)
                ->addViolation();
        }
    }
}
