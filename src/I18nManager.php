<?php

declare(strict_types=1);

namespace BinSoul\Symfony\Bundle\I18n;

use BinSoul\Common\I18n\Locale;

/**
 * Manages a stack of environments configured for specific locales.
 */
interface I18nManager
{
    /**
     * Returns the active environment.
     */
    public function getEnvironment(): I18nEnvironment;

    /**
     * Pushes a new environment for the given locale to the stack of environments and returns it.
     */
    public function enterEnvironment(Locale $locale): I18nEnvironment;

    /**
     * Pops the last environment from the stack of environments and activates the previous environment.
     */
    public function leaveEnvironment(): void;

    /**
     * Pushes the default environment to the stack of environments and returns it.
     */
    public function enterDefaultEnvironment(): I18nEnvironment;

    /**
     * Executes the operation in an environment for the given locale and returns the output.
     */
    public function execute(Locale $locale, callable $operation): mixed;
}
