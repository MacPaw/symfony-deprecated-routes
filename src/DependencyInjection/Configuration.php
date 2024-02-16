<?php

declare(strict_types=1);

namespace Macpaw\SymfonyDeprecatedRoutes\DependencyInjection;

use Macpaw\SymfonyDeprecatedRoutes\Routing\EventSubscriber\DeprecationRoutesEventSubscriber;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public const HEADER_OPTION_NAME = 'headers';
    public const DEPRECATION_MESSAGE_OPTION_NAME = 'deprecated_message_name';
    public const DEPRECATION_FROM_OPTION_NAME = 'deprecated_from_name';
    public const DEPRECATION_SINCE_OPTION_NAME = 'deprecated_since_name';
    public const SINCE_HEADER_REQUIRED_OPTION_NAME = 'enableSinceHeader';
    public const IS_DISABLED_OPTION_NAME = 'isDisabled';
    public const IS_SINCE_OPTION_REQUIRED = 'isSinceRequired';

    public function getConfigTreeBuilder(): TreeBuilder
    {
        $tree = new TreeBuilder(DeprecatedRoutesExtension::NAME);
        $rootNode = $tree->getRootNode();

        $rootNode->children()
            ->arrayNode(self::HEADER_OPTION_NAME)
                ->addDefaultsIfNotSet()
                ->children()
                    ->scalarNode(self::DEPRECATION_MESSAGE_OPTION_NAME)
                        ->cannotBeEmpty()
                        ->defaultValue(DeprecationRoutesEventSubscriber::X_DEPRECATED_HEADER)
                    ->end()
                    ->scalarNode(self::DEPRECATION_FROM_OPTION_NAME)
                        ->cannotBeEmpty()
                        ->defaultValue(DeprecationRoutesEventSubscriber::X_DEPRECATED_FROM_HEADER)
                    ->end()
                    ->scalarNode(self::DEPRECATION_SINCE_OPTION_NAME)
                        ->cannotBeEmpty()
                        ->defaultValue(DeprecationRoutesEventSubscriber::X_DEPRECATED_SINCE_HEADER)
                    ->end()
                    ->booleanNode(self::SINCE_HEADER_REQUIRED_OPTION_NAME)
                        ->defaultFalse()
                    ->end()
                ->end()
            ->end()
            ->scalarNode(self::IS_DISABLED_OPTION_NAME)
                ->defaultValue(false)
            ->end()
            ->scalarNode(self::IS_SINCE_OPTION_REQUIRED)
                ->defaultValue(false)
            ->end()
        ->end()
        ;
        return $tree;
    }
}
