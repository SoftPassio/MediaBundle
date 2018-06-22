<?php

namespace AppVerk\MediaBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();

        $rootNode = $treeBuilder->root('media');

        $rootNode
            ->children()
                ->arrayNode('entities')
                    ->children()
                        ->scalarNode('media_class')->cannotBeEmpty()->end()
                    ->end()
                ->end()
                ->scalarNode('max_file_size')->defaultValue(5242880)->end()
                ->scalarNode('media_web_path')->defaultValue('/uploads/media')->end()
                ->scalarNode('media_root_dir')->defaultValue('%kernel.project_dir%/web/uploads/media')->end()
                ->arrayNode('allowed_extensions')
                    ->prototype('scalar')
                ->end()
            ->end();

        return $treeBuilder;
    }
}
