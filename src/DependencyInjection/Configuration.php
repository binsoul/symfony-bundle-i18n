<?php

declare(strict_types=1);

namespace BinSoul\Symfony\Bundle\I18n\DependencyInjection;

use BinSoul\Symfony\Bundle\I18n\Formatter\AddressFormatter;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('binsoul_i18n');

        /** @var ArrayNodeDefinition $rootNode */
        $rootNode = $treeBuilder->getRootNode();
        $rootNode
            ->children()
            ->scalarNode('prefix')
            ->defaultValue('')
            ->info('will be prepended to table, index and sequence names')
            ->end()
            ->booleanNode('enableTranslator')
            ->defaultValue(false)
            ->info('enables the database translator')
            ->end()
            ->scalarNode('defaultCountry')
            ->defaultNull()
            ->info('default country code to use')
            ->end()
            ->scalarNode('addressFormatter')
            ->defaultValue(AddressFormatter::class)
            ->info('service id of the address formatter to use')
            ->end()
            ->end();

        return $treeBuilder;
    }
}
