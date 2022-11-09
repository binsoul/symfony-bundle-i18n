<?php

declare(strict_types=1);

namespace BinSoul\Test\Symfony\Bundle\I18n\Twig\Extension;

use BinSoul\Symfony\Bundle\I18n\Service\Manager;
use BinSoul\Symfony\Bundle\I18n\Twig\Extension\AddressFormatterExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Translation\Translator;

class AddressFormatterExtensionTest extends TestCase
{
    public function test_generates_container_classes(): void
    {
        $extension = new AddressFormatterExtension($this->buildManager());

        $classes = $extension->addressContainerClasses('de');
        $this->assertEquals('rows-7 columns-3', $classes);
    }

    public function test_generates_field_classes(): void
    {
        $extension = new AddressFormatterExtension($this->buildManager());

        $classes = $extension->addressFieldClasses('addressLine1', 'de');
        $this->assertEquals('visible row-3 column-1 span-3', $classes);
        $classes = $extension->addressFieldClasses('postalCode', 'de');
        $this->assertEquals('visible row-6 column-1 span-1', $classes);
        $classes = $extension->addressFieldClasses('locality', 'de');
        $this->assertEquals('visible row-6 column-2 span-2', $classes);
        $classes = $extension->addressFieldClasses('state', 'de');
        $this->assertEquals('invisible', $classes);
    }

    private function buildManager(): Manager
    {
        /** @var Translator $translator */
        $translator = $this->createMock(Translator::class);

        return new Manager($translator);
    }
}
