<?php

namespace Ecosystem\StatificationBundle;

use Ecosystem\StatificationBundle\Service\SettingService;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class EcosystemStatificationBundle extends AbstractBundle
{
    public function loadExtension(
        array $config,
        ContainerConfigurator $containerConfigurator,
        ContainerBuilder $containerBuilder
    ): void {
        $containerConfigurator->import('../config/services.yaml');
        $containerConfigurator->services()->get(SettingService::class)->arg(0, $config['bucket']);
        $containerConfigurator->services()->get(SettingService::class)->arg(1, $config['key'] ?? null);
        $containerConfigurator->services()->get(SettingService::class)->arg(2, $config['secret'] ?? null);
    }

    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->import('../config/definition.php');
    }
}
