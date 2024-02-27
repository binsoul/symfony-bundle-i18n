<?php

declare(strict_types=1);

namespace BinSoul\Symfony\Bundle\I18n;

use BinSoul\Symfony\Bundle\I18n\DependencyInjection\Compiler\OverrideTranslatorPass;
use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class BinsoulI18nBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new OverrideTranslatorPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, 16);

        $ormCompilerClass = 'Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass';

        if (class_exists($ormCompilerClass)) {
            $container->addCompilerPass(
                DoctrineOrmMappingsPass::createAttributeMappingDriver(
                    ['BinSoul\Symfony\Bundle\I18n'],
                    [realpath(__DIR__ . '/Entity')],
                )
            );
        }
    }
}
