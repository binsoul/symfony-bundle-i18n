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

        self::assertEquals(DefaultLocale::fromString(Locale::getDefault())->getCode('_'), $manager->getLocale());
        self::assertEquals(DefaultLocale::fromString(Locale::getDefault())->getCode('_'), $manager->getEnvironment()->getLocale()->getCode('_'));

        $manager->setLocale('ar_EG');

        self::assertEquals('ar_EG', $manager->getLocale());
        self::assertEquals('ar_EG', $manager->getEnvironment()->getLocale()->getCode('_'));
    }

    public function test_handles_stack(): void
    {
        $manager = $this->buildManager();

        $manager->setLocale('de_DE');
        self::assertEquals('de-DE', $manager->getEnvironment()->getLocale()->getCode());

        $environment = $manager->enterEnvironment(DefaultLocale::fromString('en-US'));
        self::assertEquals('en-US', $environment->getLocale()->getCode());
        self::assertEquals('en-US', $manager->getEnvironment()->getLocale()->getCode());

        $environment = $manager->enterEnvironment(DefaultLocale::fromString('fr'));
        self::assertEquals('fr', $environment->getLocale()->getCode());
        self::assertEquals('fr', $manager->getEnvironment()->getLocale()->getCode());

        $manager->leaveEnvironment();
        self::assertEquals('en-US', $manager->getEnvironment()->getLocale()->getCode());

        $environment = $manager->enterDefaultEnvironment();
        self::assertEquals('de-DE', $environment->getLocale()->getCode());
        self::assertEquals('de-DE', $manager->getEnvironment()->getLocale()->getCode());

        $manager->leaveEnvironment();
        self::assertEquals('en-US', $manager->getEnvironment()->getLocale()->getCode());

        $manager->leaveEnvironment();
        self::assertEquals('de-DE', $manager->getEnvironment()->getLocale()->getCode());

        $manager->leaveEnvironment();
        self::assertEquals('de-DE', $manager->getEnvironment()->getLocale()->getCode());
    }

    public function test_executes_operations(): void
    {
        $manager = $this->buildManager();

        $manager->setLocale('de_DE');
        self::assertEquals('de-DE', $manager->getEnvironment()->getLocale()->getCode());

        $called = false;
        $calledLocale = null;
        $operation = static function (I18nEnvironment $environment) use (&$called, &$calledLocale): string {
            $called = true;
            $calledLocale = $environment->getLocale()->getCode();

            return 'test';
        };

        $result = $manager->execute(DefaultLocale::fromString('en-US'), $operation);

        self::assertTrue($called);
        self::assertEquals('en-US', $calledLocale);
        self::assertEquals('test', $result);

        self::assertEquals('de-DE', $manager->getEnvironment()->getLocale()->getCode());
    }

    private function buildManager(): Manager
    {
        /** @var Translator $translator */
        $translator = $this->createMock(Translator::class);

        return new Manager($translator);
    }
}
