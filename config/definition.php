<?php

use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;

return static function (DefinitionConfigurator $definition) {
    $definition->rootNode()
        ->children()
            ->scalarNode('bucket')->end()
            ->scalarNode('key')->end()
            ->scalarNode('secret')->end()
        ->end()
    ;
};