<?php

declare(strict_types=1);

namespace BinSoul\Symfony\Bundle\I18n\DependencyInjection\Compiler;

use BinSoul\Symfony\Bundle\I18n\Repository\LocaleRepository;
use BinSoul\Symfony\Bundle\I18n\Repository\MessageRepository;
use BinSoul\Symfony\Bundle\I18n\Translation\DatabaseTranslator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Replaces the Symfony FrameworkBundle translator with the {@see DatabaseTranslator}.
 */
class OverrideTranslatorPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (! $container->hasDefinition('translator.default')) {
            return;
        }

        $defaultTranslator = $container->getDefinition('translator.default');
        $defaultTranslator->setClass(DatabaseTranslator::class);
        $defaultTranslator->addArgument(new Reference(MessageRepository::class));
        $defaultTranslator->addArgument(new Reference(LocaleRepository::class));

        $container->removeDefinition(DatabaseTranslator::class);
    }
}
