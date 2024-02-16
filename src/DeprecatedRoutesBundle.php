<?php

declare(strict_types=1);

namespace Macpaw\SymfonyDeprecatedRoutes;

use Macpaw\SymfonyDeprecatedRoutes\DependencyInjection\DeprecatedRoutesExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class DeprecatedRoutesBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
    }

    protected function createContainerExtension(): ExtensionInterface
    {
        return new DeprecatedRoutesExtension();
    }

    public function getContainerExtension(): ExtensionInterface
    {
        return $this->createContainerExtension();
    }
}
