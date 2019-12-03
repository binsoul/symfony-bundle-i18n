<?php

declare(strict_types=1);

namespace BinSoul\Symfony\Bundle\I18n\DependencyInjection;

use BinSoul\Symfony\Bundle\I18n\EventListener\TablePrefixListener;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class BinsoulI18nExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yaml');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $definition = $container->getDefinition(TablePrefixListener::class);
        $definition->setArgument(0, $config['prefix']);
    }
}