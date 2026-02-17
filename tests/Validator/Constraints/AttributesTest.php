<?php

declare(strict_types=1);

namespace BinSoul\Test\Symfony\Bundle\I18n\Validator\Constraints;

use BinSoul\Symfony\Bundle\I18n\Validator\Constraints\AddressFieldValidator;
use BinSoul\Symfony\Bundle\I18n\Validator\Constraints\AddressLine1;
use BinSoul\Symfony\Bundle\I18n\Validator\Constraints\AddressLine2;
use BinSoul\Symfony\Bundle\I18n\Validator\Constraints\AddressLine3;
use BinSoul\Symfony\Bundle\I18n\Validator\Constraints\Country;
use BinSoul\Symfony\Bundle\I18n\Validator\Constraints\CountryValidator;
use BinSoul\Symfony\Bundle\I18n\Validator\Constraints\Locality;
use BinSoul\Symfony\Bundle\I18n\Validator\Constraints\PostalCode;
use BinSoul\Symfony\Bundle\I18n\Validator\Constraints\SortingCode;
use BinSoul\Symfony\Bundle\I18n\Validator\Constraints\State;
use BinSoul\Symfony\Bundle\I18n\Validator\Constraints\StateValidator;
use PHPUnit\Framework\TestCase;

class AttributesTest extends TestCase
{
    public function test_static_address_field_values_and_validators(): void
    {
        $this->assertSame('addressLine1', AddressLine1::$addressField);
        $this->assertSame('addressLine2', AddressLine2::$addressField);
        $this->assertSame('addressLine3', AddressLine3::$addressField);
        $this->assertSame('locality', Locality::$addressField);
        $this->assertSame('postalCode', PostalCode::$addressField);
        $this->assertSame('sortingCode', SortingCode::$addressField);
        $this->assertSame('state', State::$addressField);

        $this->assertSame(AddressFieldValidator::class, (new AddressLine1())->validatedBy());
        $this->assertSame(AddressFieldValidator::class, (new AddressLine2())->validatedBy());
        $this->assertSame(AddressFieldValidator::class, (new AddressLine3())->validatedBy());
        $this->assertSame(AddressFieldValidator::class, (new Locality())->validatedBy());
        $this->assertSame(AddressFieldValidator::class, (new PostalCode())->validatedBy());
        $this->assertSame(AddressFieldValidator::class, (new SortingCode())->validatedBy());
        $this->assertSame(StateValidator::class, (new State())->validatedBy());
        $this->assertSame(CountryValidator::class, (new Country())->validatedBy());
    }

    public function test_constructor_defaults_and_overrides_on_abstract_constraint_children(): void
    {
        $constraint = new PostalCode(groups: ['Custom'], countryField: 'country', countryCodeLiteral: 'DE', forceOptional: true, forceRequired: true);

        $this->assertSame(['Custom'], $constraint->groups);
        $this->assertSame('country', $constraint->countryField);
        $this->assertSame('DE', $constraint->countryCodeLiteral);
        $this->assertTrue($constraint->forceOptional);
        $this->assertTrue($constraint->forceRequired);

        // Default messages
        $this->assertSame('This value should not be blank.', $constraint->messageNotBlank);
        $this->assertSame('This value is not valid.', $constraint->messageRegex);
        $this->assertSame('The country "{{ country }}" is not supported.', $constraint->messageCountryNotSupported);

        // Payload should be assignable (last arg)
        $payloadConstraint = new PostalCode(payload: ['foo' => 'bar']);
        $this->assertSame(['foo' => 'bar'], $payloadConstraint->payload);
    }

    public function test_state_specific_message_is_present(): void
    {
        $state = new State();
        $this->assertSame('This value is not a valid state.', $state->messageInvalidState);
    }

    public function test_country_defaults_and_overrides(): void
    {
        $constraint = new Country(groups: ['Custom'], message: 'Invalid country', payload: ['a' => 'b']);

        $this->assertSame(['Custom'], $constraint->groups);
        $this->assertSame('Invalid country', $constraint->message);
        $this->assertSame(['a' => 'b'], $constraint->payload);
    }
}
