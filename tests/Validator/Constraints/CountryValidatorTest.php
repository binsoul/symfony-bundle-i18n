<?php

declare(strict_types=1);

namespace BinSoul\Test\Symfony\Bundle\I18n\Validator\Constraints;

use BinSoul\Symfony\Bundle\I18n\Validator\Constraints\AddressValidationHelper;
use BinSoul\Symfony\Bundle\I18n\Validator\Constraints\Country;
use BinSoul\Symfony\Bundle\I18n\Validator\Constraints\CountryValidator;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

class CountryValidatorTest extends TestCase
{
    private AddressValidationHelper&Stub $helper;

    private CountryValidator $validator;

    protected function setUp(): void
    {
        $this->helper = $this->createStub(AddressValidationHelper::class);
        $this->validator = new CountryValidator($this->helper);
    }

    public function test_validate_throws_on_invalid_constraint_type(): void
    {
        $context = $this->createStub(ExecutionContextInterface::class);
        $this->validator->initialize($context);

        $this->expectException(UnexpectedTypeException::class);
        $this->validator->validate('DE', $this->createStub(Constraint::class));
    }

    public function test_null_or_empty_value_skips_validation(): void
    {
        $context = $this->createMock(ExecutionContextInterface::class);
        $context->expects($this->never())->method('buildViolation');
        $this->validator->initialize($context);

        $constraint = new Country();

        $this->helper->method('normalizeValue')->willReturn(null);
        $this->validator->validate(null, $constraint);

        $this->helper->method('normalizeValue')->willReturn(null);
        $this->validator->validate('', $constraint);
    }

    public function test_valid_country_code_has_no_violation(): void
    {
        $context = $this->createMock(ExecutionContextInterface::class);
        $context->expects($this->never())->method('buildViolation');
        $this->validator->initialize($context);

        $constraint = new Country();

        $this->helper->method('normalizeValue')->willReturn('DE');
        $this->validator->validate('DE', $constraint);

        $this->helper->method('normalizeValue')->willReturn('us');
        $this->validator->validate('us', $constraint);
    }

    public function test_invalid_country_code_adds_violation(): void
    {
        $context = $this->createMock(ExecutionContextInterface::class);
        $this->validator->initialize($context);

        $constraint = new Country();

        $this->helper->method('normalizeValue')->willReturn('ZZ');

        $builder = $this->createMock(ConstraintViolationBuilderInterface::class);
        $builder->expects($this->once())->method('setParameter')->with('{{ country }}', 'ZZ')->willReturnSelf();
        $builder->expects($this->once())->method('addViolation');

        $context->expects($this->once())
            ->method('buildViolation')
            ->with($constraint->message)
            ->willReturn($builder);

        $this->validator->validate('ZZ', $constraint);
    }

    public function test_valid_country_entity_has_no_violation(): void
    {
        $context = $this->createMock(ExecutionContextInterface::class);
        $context->expects($this->never())->method('buildViolation');
        $this->validator->initialize($context);

        $constraint = new Country();

        $countryEntity = new class() {
            public function getIso2(): string
            {
                return 'IT';
            }
        };

        $this->validator->validate($countryEntity, $constraint);
    }
}
