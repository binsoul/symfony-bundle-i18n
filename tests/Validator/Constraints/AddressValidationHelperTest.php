<?php

declare(strict_types=1);

namespace BinSoul\Test\Symfony\Bundle\I18n\Validator\Constraints;

use BinSoul\Common\I18n\Address;
use BinSoul\Common\I18n\AddressFormatter;
use BinSoul\Symfony\Bundle\I18n\Validator\Constraints\AddressValidationConfig;
use BinSoul\Symfony\Bundle\I18n\Validator\Constraints\AddressValidationHelper;
use BinSoul\Symfony\Bundle\I18n\Validator\Constraints\PostalCode;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class AddressValidationHelperTest extends TestCase
{
    private AddressFormatter&Stub $formatter;

    private AddressValidationConfig $config;

    private AddressValidationHelper $helper;

    protected function setUp(): void
    {
        $this->formatter = $this->createStub(AddressFormatter::class);
        $this->config = new AddressValidationConfig($this->formatter, 'US');
        $this->helper = new AddressValidationHelper($this->config);
    }

    public function test_resolve_country_code_precedence(): void
    {
        $context = $this->createStub(ExecutionContextInterface::class);
        $constraint = new PostalCode();

        $usageTemplateDE = $this->createStub(Address::class);
        $usageTemplateDE->method('getCountryCode')->willReturn('DE');
        $usageTemplateUS = $this->createStub(Address::class);
        $usageTemplateUS->method('getCountryCode')->willReturn('US');
        $usageTemplateFR = $this->createStub(Address::class);
        $usageTemplateFR->method('getCountryCode')->willReturn('FR');
        $usageTemplateGB = $this->createStub(Address::class);
        $usageTemplateGB->method('getCountryCode')->willReturn('GB');

        $this->formatter->method('generateUsageTemplate')->willReturnMap([
            ['DE', $usageTemplateDE],
            ['US', $usageTemplateUS],
            ['FR', $usageTemplateFR],
            ['GB', $usageTemplateGB],
            ['ZZ', $this->createStub(Address::class)],
        ]);

        // 4. Default country
        $this->assertSame(['US', 'US'], $this->helper->resolveCountryCode($constraint, $context));

        // 3. Country field
        $constraint->countryField = 'country';
        $object = new class() {
            public string $country = 'DE';
        };
        $context->method('getObject')->willReturn($object);
        $this->assertSame(['DE', 'DE'], $this->helper->resolveCountryCode($constraint, $context));

        // 2. Country resolver
        $constraint->countryResolver = fn () => 'FR';
        $this->assertSame(['FR', 'FR'], $this->helper->resolveCountryCode($constraint, $context));

        // 1. Country code literal
        $constraint->countryCodeLiteral = 'GB';
        $this->assertSame(['GB', 'GB'], $this->helper->resolveCountryCode($constraint, $context));

        // Unsupported country
        $constraint->countryCodeLiteral = 'ZZ';
        $this->assertSame([null, 'ZZ'], $this->helper->resolveCountryCode($constraint, $context));
    }

    public function test_resolve_country_code_with_country_entity(): void
    {
        $context = $this->createStub(ExecutionContextInterface::class);
        $constraint = new PostalCode(countryField: 'country');

        $usageTemplateIT = $this->createStub(Address::class);
        $usageTemplateIT->method('getCountryCode')->willReturn('IT');
        // Using a stub: avoid with() to prevent PHPUnit deprecation; just return the template.
        $this->formatter->method('generateUsageTemplate')->willReturn($usageTemplateIT);

        $countryEntity = new class() {
            public function getIso2()
            {
                return 'IT';
            }
        };
        $object = new class($countryEntity) {
            public function __construct(
                public $country
            ) {
            }
        };
        $context->method('getObject')->willReturn($object);

        $this->assertSame(['IT', 'IT'], $this->helper->resolveCountryCode($constraint, $context));
    }

    public function test_resolve_country_code_with_stringable_object(): void
    {
        $context = $this->createStub(ExecutionContextInterface::class);
        $constraint = new PostalCode(countryField: 'country');

        $stringable = new class() {
            public function __toString()
            {
                return 'it';
            }
        };
        $object = new class($stringable) {
            public function __construct(
                public $country
            ) {
            }
        };
        $context->method('getObject')->willReturn($object);

        $this->assertSame(['IT', 'IT'], $this->helper->resolveCountryCode($constraint, $context));
    }


    public function test_resolve_country_code_property_accessor_exception_is_caught(): void
    {
        $context = $this->createStub(ExecutionContextInterface::class);
        $constraint = new PostalCode(countryField: 'invalid['); // invalid path triggers PropertyAccessor exception

        $this->formatter->method('generateUsageTemplate')->willReturn($this->createStub(Address::class));

        $object = new class() {};
        $context->method('getObject')->willReturn($object);

        $this->assertSame(['US', 'US'], $this->helper->resolveCountryCode($constraint, $context));
    }

    public function test_get_templates_caches_results(): void
    {
        $country = 'DE';
        $field = 'postalCode';

        $usageTemplate = $this->createStub(Address::class);
        $usageTemplate->method('getPostalCode')->willReturn('required');

        $regexTemplate = $this->createStub(Address::class);
        $regexTemplate->method('getPostalCode')->willReturn('\d{5}');

        $formatter = $this->createMock(AddressFormatter::class);
        $formatter->expects($this->once())->method('generateUsageTemplate')->with($country)->willReturn($usageTemplate);
        $formatter->expects($this->once())->method('generateRegexTemplate')->with($country)->willReturn($regexTemplate);

        $config = new AddressValidationConfig($formatter, 'US');
        $helper = new AddressValidationHelper($config);

        // First call - should call formatter
        $result1 = $helper->getTemplates($country, $field);
        $this->assertEquals(['required', '\d{5}'], $result1);

        // Second call - should use cache for templates, but still call the getter on the template
        $result2 = $helper->getTemplates($country, $field);
        $this->assertEquals(['required', '\d{5}'], $result2);
    }

    public function test_is_required(): void
    {
        $constraint = new PostalCode();

        $this->assertTrue($this->helper->isRequired($constraint, 'required'));
        $this->assertFalse($this->helper->isRequired($constraint, 'optional'));
        $this->assertFalse($this->helper->isRequired($constraint, null));

        $constraint->forceOptional = true;
        $this->assertFalse($this->helper->isRequired($constraint, 'required'));

        $constraint->forceRequired = true;
        $this->assertTrue($this->helper->isRequired($constraint, 'optional'));
    }

    public function test_normalize_value(): void
    {
        $this->assertNull($this->helper->normalizeValue(null, null));
        $this->assertSame('123', $this->helper->normalizeValue(123, null));
        $this->assertSame('foo', $this->helper->normalizeValue('foo', null));

        $obj = new class() {
            public function __toString()
            {
                return 'bar';
            }
        };
        $this->assertSame('bar', $this->helper->normalizeValue($obj, null));

        $normalizer = fn ($v) => strtoupper($v);
        $this->assertSame('FOO', $this->helper->normalizeValue('foo', $normalizer));
    }

    public function test_normalize_value_throws_on_invalid_type(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->helper->normalizeValue([], null);
    }

    public function test_apply_regex(): void
    {
        $this->assertTrue($this->helper->applyRegex('12345', '\d{5}'));
        $this->assertFalse($this->helper->applyRegex('123A5', '^\d{5}$'));
        $this->assertTrue($this->helper->applyRegex('foo', null));
        $this->assertTrue($this->helper->applyRegex('foo', ''));
    }
}
