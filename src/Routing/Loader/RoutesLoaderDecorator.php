<?php

declare(strict_types=1);

namespace Macpaw\SymfonyDeprecatedRoutes\Routing\Loader;

use Macpaw\SymfonyDeprecatedRoutes\Routing\AnnotationReader\MarkDeprecatedRoutes;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\Routing\RouteCollection;

final class RoutesLoaderDecorator implements LoaderInterface
{
    public function __construct(
        private LoaderInterface $loader,
        private MarkDeprecatedRoutes $annotationReader,
    ) {
    }

    public function load(mixed $resource, ?string $type = null): RouteCollection
    {
        $routes = $this->loader->load($resource, $type);
        assert($routes instanceof RouteCollection);

        $this->annotationReader->updateRoutes($routes);

        return $routes;
    }

    public function supports(mixed $resource, ?string $type = null): bool
    {
        return $this->loader->supports($resource, $type);
    }

    public function getResolver(): LoaderResolverInterface
    {
        return $this->loader->getResolver();
    }

    public function setResolver(LoaderResolverInterface $resolver): void
    {
        $this->loader->setResolver($resolver);
    }
}
