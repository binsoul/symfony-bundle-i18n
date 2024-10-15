<?php

declare(strict_types=1);

namespace BinSoul\Symfony\Bundle\I18n;

use BinSoul\Symfony\Bundle\I18n\DependencyInjection\Compiler\OverrideTranslatorPass;
use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Doctrine\ORM\Mapping\Driver\AttributeDriver;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class BinsoulI18nBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new OverrideTranslatorPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, 16);

        $container->addCompilerPass(
            $this->buildDoctrineOrmMappingsPass(
                ['BinSoul\Symfony\Bundle\I18n'],
                [realpath(__DIR__ . '/Entity')],
            )
        );
    }

    private function buildDoctrineOrmMappingsPass(array $namespaces, array $directories): DoctrineOrmMappingsPass
    {
        $driver = new Definition(AttributeDriver::class, [$directories]);

        return new DoctrineOrmMappingsPass($driver, $namespaces, [], false, []);
    }
}
