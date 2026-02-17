<?php

declare(strict_types=1);

namespace BinSoul\Test\Symfony\Bundle\I18n\Validator\Constraints;

use BinSoul\Symfony\Bundle\I18n\Validator\Constraints\AddressFieldValidator;
use BinSoul\Symfony\Bundle\I18n\Validator\Constraints\AddressValidationHelper;
use BinSoul\Symfony\Bundle\I18n\Validator\Constraints\PostalCode;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

class AddressFieldValidatorTest extends TestCase
{
    private AddressValidationHelper&Stub $helper;

    protected function setUp(): void
    {
        $this->helper = $this->createStub(AddressValidationHelper::class);
        $this->validator = new AddressFieldValidator($this->helper);
    }

    public function test_builds_violation_when_country_cannot_be_resolved(): void
    {
        $context = $this->createMock(ExecutionContextInterface::class);
        $this->validator->initialize($context);
        $constraint = new PostalCode();

        $this->helper->method('normalizeValue')->willReturn('10115');
        $this->helper->method('resolveCountryCode')->willReturn([null, 'zz']);

        $builder = $this->createMock(ConstraintViolationBuilderInterface::class);
        $builder->expects($this->once())->method('setParameter')->with('{{ country }}', 'ZZ')->willReturnSelf();
        $builder->expects($this->once())->method('addViolation');

        $context->expects($this->once())
            ->method('buildViolation')
            ->with($constraint->messageCountryNotSupported)
            ->willReturn($builder);

        $this->validator->validate('10115', $constraint);
    }

    public function test_returns_early_when_field_hidden(): void
    {
        $context = $this->createMock(ExecutionContextInterface::class);
        $context->expects($this->never())->method('buildViolation');
        $this->validator->initialize($context);

        $constraint = new PostalCode();

        $this->helper->method('normalizeValue')->willReturn('10115');
        $this->helper->method('resolveCountryCode')->willReturn(['DE', 'DE']);
        $this->helper->method('getTemplates')->willReturn([null, null]);

        $this->validator->validate('10115', $constraint);
    }

    public function test_required_blank_adds_not_blank_violation(): void
    {
        $context = $this->createMock(ExecutionContextInterface::class);
        $this->validator->initialize($context);
        $constraint = new PostalCode();

        $this->helper->method('normalizeValue')->willReturn('');
        $this->helper->method('resolveCountryCode')->willReturn(['DE', 'DE']);
        $this->helper->method('getTemplates')->willReturn(['required', null]);
        $this->helper->method('isRequired')->willReturn(true);

        $builder = $this->createMock(ConstraintViolationBuilderInterface::class);
        $builder->expects($this->once())->method('addViolation');

        $context->expects($this->once())
            ->method('buildViolation')
            ->with($constraint->messageNotBlank)
            ->willReturn($builder);

        $this->validator->validate('', $constraint);
    }

    public function test_optional_blank_skips_regex_and_has_no_violation(): void
    {
        $context = $this->createMock(ExecutionContextInterface::class);
        $context->expects($this->never())->method('buildViolation');
        $this->validator->initialize($context);

        $constraint = new PostalCode();

        $this->helper->method('normalizeValue')->willReturn('');
        $this->helper->method('resolveCountryCode')->willReturn(['DE', 'DE']);
        $this->helper->method('getTemplates')->willReturn(['optional', '\\d{5}']);
        $this->helper->method('isRequired')->willReturn(false);

        $this->validator->validate('', $constraint);
    }

    public function test_regex_mismatch_adds_violation(): void
    {
        $context = $this->createMock(ExecutionContextInterface::class);
        $this->validator->initialize($context);
        $constraint = new PostalCode();

        $this->helper->method('normalizeValue')->willReturn('ABCDE');
        $this->helper->method('resolveCountryCode')->willReturn(['DE', 'DE']);
        $this->helper->method('getTemplates')->willReturn(['optional', '\\d{5}']);
        $this->helper->method('isRequired')->willReturn(false);
        $this->helper->method('applyRegex')->willReturn(false);

        $builder = $this->createMock(ConstraintViolationBuilderInterface::class);
        $builder->expects($this->once())->method('addViolation');
        $context->expects($this->once())
            ->method('buildViolation')
            ->with($constraint->messageRegex)
            ->willReturn($builder);

        $this->validator->validate('ABCDE', $constraint);
    }

    public function test_validate_throws_on_invalid_constraint_type(): void
    {
        $context = $this->createStub(ExecutionContextInterface::class);
        $validator = new AddressFieldValidator($this->helper);
        $validator->initialize($context);
        $this->expectException(UnexpectedTypeException::class);
        $validator->validate('value', $this->createStub(Constraint::class));
    }

    public function test_builds_violation_when_country_cannot_be_resolved_with_na(): void
    {
        $context = $this->createMock(ExecutionContextInterface::class);
        $this->validator->initialize($context);
        $constraint = new PostalCode();

        $this->helper->method('normalizeValue')->willReturn('10115');
        $this->helper->method('resolveCountryCode')->willReturn([null, null]);

        $builder = $this->createMock(ConstraintViolationBuilderInterface::class);
        $builder->expects($this->once())->method('setParameter')->with('{{ country }}', 'n/a')->willReturnSelf();
        $builder->expects($this->once())->method('addViolation');

        $context->expects($this->once())
            ->method('buildViolation')
            ->with($constraint->messageCountryNotSupported)
            ->willReturn($builder);

        $this->validator->validate('10115', $constraint);
    }
}
