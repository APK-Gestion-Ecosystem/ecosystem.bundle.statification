<?php

namespace Ecosystem\StatificationBundle;

use Ecosystem\StatificationBundle\Service\SettingService;
use Ecosystem\StatificationBundle\Service\WidgetService;
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
        $containerConfigurator->services()->get(WidgetService::class)->arg(0, $config['bucket']);
    }

    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->import('../config/definition.php');
    }
}
