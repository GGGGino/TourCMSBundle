<?php

namespace GGGGino\TourCMSBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('ggggino_tourcms');

        $rootNode
            ->children()
                ->integerNode('marketplace_id')->end()
                ->scalarNode('api_key')->end()
                ->integerNode('timeout')->end()
                ->integerNode('channel_id')->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
