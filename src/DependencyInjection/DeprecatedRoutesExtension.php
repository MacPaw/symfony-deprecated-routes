<?php

declare(strict_types=1);

namespace Macpaw\SymfonyDeprecatedRoutes\DependencyInjection;

use Exception;
use Macpaw\SymfonyDeprecatedRoutes\DeprecatedRoutesBundle;
use Macpaw\SymfonyDeprecatedRoutes\Routing\AnnotationReader\MarkDeprecatedRoutes;
use Macpaw\SymfonyDeprecatedRoutes\Routing\EventSubscriber\DeprecationRoutesEventSubscriber;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

final class DeprecatedRoutesExtension extends Extension
{
    public const NAME = 'deprecated-routes';

    /**
     * @throws Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../../Resource/config'));
        $loader->load('services.yaml');

        $configuration = $this->getConfiguration($configs, $container);
        assert($configuration instanceof ConfigurationInterface);
        $configs = $this->processConfiguration($configuration, $configs);
        $definition = $container->getDefinition(DeprecationRoutesEventSubscriber::class);

        $headers = $configs[Configuration::HEADER_OPTION_NAME];
        $definition->setArguments([
            '$deprecationHeaderName' => $headers[Configuration::DEPRECATION_MESSAGE_OPTION_NAME],
            '$deprecationFromHeaderName' => $headers[Configuration::DEPRECATION_FROM_OPTION_NAME],
            '$deprecationSinceHeaderName' => $headers[Configuration::DEPRECATION_SINCE_OPTION_NAME],
            '$isDisabled' => $configs[Configuration::IS_DISABLED_OPTION_NAME],
        ]);

        $container->setDefinition(DeprecationRoutesEventSubscriber::class, $definition);

        $definition = $container->getDefinition(MarkDeprecatedRoutes::class);
        $definition->setArguments([
            '$sinceOptionIsRequired' => $configs[Configuration::IS_SINCE_OPTION_REQUIRED],
        ]);
    }

    /**
     * @param array<string, mixed> $config
     */
    public function getConfiguration(array $config, ContainerBuilder $container)
    {
        return new Configuration();
    }

    public function getAlias(): string
    {
        return self::NAME;
    }
}
