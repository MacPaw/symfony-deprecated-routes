<?php

declare(strict_types=1);

namespace Macpaw\SymfonyDeprecatedRoutes\Tests\Unit;

use Macpaw\SymfonyDeprecatedRoutes\Routing\AnnotationReader\MarkDeprecatedRoutes;
use Macpaw\SymfonyDeprecatedRoutes\Routing\Exception\SinceHeaderRequiredException;
use Macpaw\SymfonyDeprecatedRoutes\Routing\Loader\RoutesLoaderDecorator;
use Macpaw\SymfonyDeprecatedRoutes\Tests\App\Controller\HomeController;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

final class RouteLoaderTest extends TestCase
{
    public function testSuccessfullyRouteMarked(): void
    {
        $routeCollection = new RouteCollection();
        $routeCollection->add(
            'home',
            new Route(
                path: '/',
                defaults: [
                    '_controller' => sprintf('%s::home', HomeController::class),
                ],
                methods: [Request::METHOD_GET]
            ),
        );
        $markDeprecatedRoutes = new MarkDeprecatedRoutes();

        $innerLoader = $this->createMock(LoaderInterface::class);
        $innerLoader
            ->method('load')
            ->willReturn($routeCollection);

        $innerLoader->method('supports')
            ->willReturn(true);

        $loaderResolver = $this->createMock(LoaderResolverInterface::class);
        $innerLoader->method('getResolver')
            ->willReturn($loaderResolver);

        /** @var LoaderInterface $innerLoader */
        $loader = new RoutesLoaderDecorator($innerLoader, $markDeprecatedRoutes);

        $routes = $loader->load('123');

        /** @var Route $homeRoute */
        $homeRoute = $routes->get('home');

        $defaults = $homeRoute->getDefaults();

        self::assertArrayHasKey(MarkDeprecatedRoutes::DEPRECATED_OPTION, $defaults);
        self::assertIsArray($defaults[MarkDeprecatedRoutes::DEPRECATED_OPTION]);
        self::assertTrue($loader->supports(''));
        self::assertIsObject($loader->getResolver());
        $loader->setResolver($loaderResolver);
        self::assertIsObject($loader->getResolver());
    }

    public function testSkipCallable(): void
    {
        $routeCollection = new RouteCollection();
        $routeCollection->add(
            'home',
            new Route(
                path: '/',
                defaults: [
                    '_controller' => static fn (): JsonResponse => new JsonResponse([]),
                ],
                methods: [Request::METHOD_GET]
            ),
        );
        $markDeprecatedRoutes = new MarkDeprecatedRoutes();

        $innerLoader = $this->createMock(LoaderInterface::class);
        $innerLoader
            ->method('load')
            ->willReturn($routeCollection);

        /** @var LoaderInterface $innerLoader */
        $loader = new RoutesLoaderDecorator($innerLoader, $markDeprecatedRoutes);

        $routes = $loader->load('123');

        /** @var Route $homeRoute */
        $homeRoute = $routes->get('home');

        $defaults = $homeRoute->getDefaults();

        self::assertArrayNotHasKey(MarkDeprecatedRoutes::DEPRECATED_OPTION, $defaults);
    }

    public function testExceptionWhenSinceOptionRequired(): void
    {
        $routeCollection = new RouteCollection();
        $routeCollection->add(
            'home',
            new Route(
                path: '/',
                defaults: [
                    '_controller' => sprintf('%s::home1', HomeController::class),
                ],
                methods: [Request::METHOD_GET]
            ),
        );
        $markDeprecatedRoutes = new MarkDeprecatedRoutes(true);

        $innerLoader = $this->createMock(LoaderInterface::class);
        $innerLoader
            ->method('load')
            ->willReturn($routeCollection);

        $innerLoader->method('supports')
            ->willReturn(true);

        $loaderResolver = $this->createMock(LoaderResolverInterface::class);
        $innerLoader->method('getResolver')
            ->willReturn($loaderResolver);

        self::expectException(SinceHeaderRequiredException::class);
        /** @var LoaderInterface $innerLoader */
        $loader = new RoutesLoaderDecorator($innerLoader, $markDeprecatedRoutes);
        $loader->load('123');
    }

    public function testSuccessfullyRouteMarkedInvokableController(): void
    {
        $routeCollection = new RouteCollection();
        $routeCollection->add(
            'home',
            new Route(
                path: '/',
                defaults: [
                    '_controller' => sprintf('%s', HomeController::class),
                ],
                methods: [Request::METHOD_GET]
            ),
        );
        $markDeprecatedRoutes = new MarkDeprecatedRoutes();

        $innerLoader = $this->createMock(LoaderInterface::class);
        $innerLoader
            ->method('load')
            ->willReturn($routeCollection);

        $innerLoader->method('supports')
            ->willReturn(true);

        $loaderResolver = $this->createMock(LoaderResolverInterface::class);
        $innerLoader->method('getResolver')
            ->willReturn($loaderResolver);

        /** @var LoaderInterface $innerLoader */
        $loader = new RoutesLoaderDecorator($innerLoader, $markDeprecatedRoutes);

        $routes = $loader->load('123');

        /** @var Route $homeRoute */
        $homeRoute = $routes->get('home');

        $defaults = $homeRoute->getDefaults();

        self::assertArrayHasKey(MarkDeprecatedRoutes::DEPRECATED_OPTION, $defaults);
        self::assertIsArray($defaults[MarkDeprecatedRoutes::DEPRECATED_OPTION]);
        self::assertTrue($loader->supports(''));
        self::assertIsObject($loader->getResolver());
    }
}
