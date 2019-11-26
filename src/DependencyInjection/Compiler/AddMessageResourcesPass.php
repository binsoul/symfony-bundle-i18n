<?php

declare(strict_types=1);

namespace BinSoul\Symfony\Bundle\I18n\DependencyInjection\Compiler;

use BinSoul\Symfony\Bundle\I18n\DataFixtures\LoadLocales;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Adds resources for all known locales.
 */
class AddMessageResourcesPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('translator.default')) {
            return;
        }

        $translator = $container->findDefinition('translator.default');
        $options = $translator->getArgument(4);
        if (!isset($options['resource_files'])) {
            $options['resource_files'] = [];
        }

        foreach (LoadLocales::$rows as $row) {
            $locale = str_replace('-', '_', $row[1]);

            if (!isset($options['resource_files'][$locale])) {
                $options['resource_files'][$locale] = [];
            }

            $options['resource_files'][$locale][] = ['messages.'.$locale.'.db'];
        }

        $translator->replaceArgument(4, $options);
    }
}
