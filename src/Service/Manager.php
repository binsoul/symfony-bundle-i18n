<?php

declare(strict_types=1);

namespace BinSoul\Symfony\Bundle\I18n\Service;

use BinSoul\Common\I18n\DefaultLocale;
use BinSoul\Common\I18n\Locale;
use BinSoul\Symfony\Bundle\I18n\Formatter\AddressFormatter;
use BinSoul\Symfony\Bundle\I18n\Formatter\DateTimeFormatter;
use BinSoul\Symfony\Bundle\I18n\Formatter\NumberFormatter;
use BinSoul\Symfony\Bundle\I18n\I18nManager;
use BinSoul\Symfony\Bundle\I18n\I18nEnvironment;
use BinSoul\Symfony\Bundle\I18n\Translation\Translator;
use Symfony\Contracts\Translation\LocaleAwareInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class Manager implements I18nManager, LocaleAwareInterface
{
    /**
     * @var Environment[]
     */
    private $stack = [];

    /**
     * @var Locale
     */
    private $defaultLocale;
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * Constructs an instance of this class.
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
        $this->defaultLocale = DefaultLocale::fromString(\Locale::getDefault());
        $this->stack[0] = $this->createEnvironment($this->defaultLocale);
    }

    public function setLocale(string $locale)
    {
        $this->defaultLocale = DefaultLocale::fromString($locale, '_');
        $this->stack[0] = $this->createEnvironment($this->defaultLocale);
        if (count($this->stack) === 1) {
            \Locale::setDefault($this->defaultLocale->getCode());
        }
    }

    public function getLocale()
    {
        return $this->defaultLocale->getCode('_');
    }

    public function getEnvironment(): I18nEnvironment
    {
        return $this->stack[count($this->stack) - 1];
    }

    public function enterDefaultEnvironment(): I18nEnvironment
    {
        return $this->enterEnvironment($this->defaultLocale);
    }

    public function enterEnvironment(Locale $locale): I18nEnvironment
    {
        $this->stack[] = $this->createEnvironment($locale);
        \Locale::setDefault($locale->getCode());

        return $this->getEnvironment();
    }

    public function leaveEnvironment(): void
    {
        if (count($this->stack) === 1) {
            return;
        }

        array_pop($this->stack);

        $environment = $this->getEnvironment();
        \Locale::setDefault($environment->getLocale()->getCode());
    }

    public function execute(Locale $locale, callable $operation)
    {
        $environment = $this->enterEnvironment($locale);

        try {
            $result = $operation($environment);
        } finally {
            $this->leaveEnvironment();
        }

        return $result;
    }

    /**
     * Returns a new environment for the given locale.
     */
    private function createEnvironment(Locale $locale): Environment
    {
        return new Environment(
            $locale,
            new NumberFormatter($locale),
            new DateTimeFormatter($locale),
            new AddressFormatter($locale),
            new Translator($this->translator, $locale)
        );
    }
}
