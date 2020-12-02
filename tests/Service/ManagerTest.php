<?php

declare(strict_types=1);

namespace BinSoul\Test\Symfony\Bundle\I18n\Service;

use BinSoul\Common\I18n\DefaultLocale;
use BinSoul\Symfony\Bundle\I18n\I18nEnvironment;
use BinSoul\Symfony\Bundle\I18n\Service\Manager;
use Locale;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Translation\Translator;

class ManagerTest extends TestCase
{
    public function test_implements_localeawareinterface(): void
    {
        $manager = $this->buildManager();

        $this->assertEquals(DefaultLocale::fromString(Locale::getDefault())->getCode('_'), $manager->getLocale());
        $this->assertEquals(DefaultLocale::fromString(Locale::getDefault())->getCode('_'), $manager->getEnvironment()->getLocale()->getCode('_'));

        $manager->setLocale('ar_EG');

        $this->assertEquals('ar_EG', $manager->getLocale());
        $this->assertEquals('ar_EG', $manager->getEnvironment()->getLocale()->getCode('_'));
    }

    public function test_handles_stack(): void
    {
        $manager = $this->buildManager();

        $manager->setLocale('de_DE');
        $this->assertEquals('de-DE', $manager->getEnvironment()->getLocale()->getCode());

        $environment = $manager->enterEnvironment(DefaultLocale::fromString('en-US'));
        $this->assertEquals('en-US', $environment->getLocale()->getCode());
        $this->assertEquals('en-US', $manager->getEnvironment()->getLocale()->getCode());

        $environment = $manager->enterEnvironment(DefaultLocale::fromString('fr'));
        $this->assertEquals('fr', $environment->getLocale()->getCode());
        $this->assertEquals('fr', $manager->getEnvironment()->getLocale()->getCode());

        $manager->leaveEnvironment();
        $this->assertEquals('en-US', $manager->getEnvironment()->getLocale()->getCode());

        $environment = $manager->enterDefaultEnvironment();
        $this->assertEquals('de-DE', $environment->getLocale()->getCode());
        $this->assertEquals('de-DE', $manager->getEnvironment()->getLocale()->getCode());

        $manager->leaveEnvironment();
        $this->assertEquals('en-US', $manager->getEnvironment()->getLocale()->getCode());

        $manager->leaveEnvironment();
        $this->assertEquals('de-DE', $manager->getEnvironment()->getLocale()->getCode());

        $manager->leaveEnvironment();
        $this->assertEquals('de-DE', $manager->getEnvironment()->getLocale()->getCode());
    }

    public function test_executes_operations(): void
    {
        $manager = $this->buildManager();

        $manager->setLocale('de_DE');
        $this->assertEquals('de-DE', $manager->getEnvironment()->getLocale()->getCode());

        $called = false;
        $calledLocale = null;
        $operation = static function (I18nEnvironment $environment) use (&$called, &$calledLocale) {
            $called = true;
            $calledLocale = $environment->getLocale()->getCode();

            return 'test';
        };

        $result = $manager->execute(DefaultLocale::fromString('en-US'), $operation);

        $this->assertTrue($called);
        $this->assertEquals('en-US', $calledLocale);
        $this->assertEquals('test', $result);

        $this->assertEquals('de-DE', $manager->getEnvironment()->getLocale()->getCode());
    }

    private function buildManager(): Manager
    {
        /** @var Translator $translator */
        $translator = $this->createMock(Translator::class);

        return new Manager($translator);
    }
}
