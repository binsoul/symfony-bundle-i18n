<?php

declare(strict_types=1);

namespace BinSoul\Test\Symfony\Bundle\I18n\Validator\Constraints;

use BinSoul\Common\I18n\AddressFormatter;
use BinSoul\Symfony\Bundle\I18n\Validator\Constraints\AddressValidationConfig;
use PHPUnit\Framework\TestCase;

class AddressValidationConfigTest extends TestCase
{
    public function test_constructor_and_getters(): void
    {
        $formatter = $this->createStub(AddressFormatter::class);
        $config = new AddressValidationConfig($formatter, 'DE');

        $this->assertSame($formatter, $config->addressFormatter);
        $this->assertSame('DE', $config->defaultCountry);
    }

    public function test_default_country_is_null_by_default(): void
    {
        $formatter = $this->createStub(AddressFormatter::class);
        $config = new AddressValidationConfig($formatter);

        $this->assertNull($config->defaultCountry);
    }
}
