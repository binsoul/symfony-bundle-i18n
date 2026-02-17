<?php

declare(strict_types=1);

namespace BinSoul\Symfony\Bundle\I18n\DependencyInjection;

use BinSoul\Symfony\Bundle\I18n\EventListener\TablePrefixListener;
use BinSoul\Symfony\Bundle\I18n\Validator\Constraints\AddressValidationConfig;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;

class BinsoulI18nExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yaml');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        $prefix = trim($config['prefix'] ?? '');

        $definition = $container->getDefinition(TablePrefixListener::class);
        $definition->setArgument(0, $prefix);

        if ($prefix === '') {
            $definition->clearTags();
        }

        $container->setParameter('binsoul_i18n.enableTranslator', $config['enableTranslator'] ?? false);

        $configDefinition = new Definition(AddressValidationConfig::class);
        $configDefinition->setArguments([
            new Reference($config['addressFormatter']),
            $config['defaultCountry'],
        ]);

        $container->setDefinition(AddressValidationConfig::class, $configDefinition);
    }
}
