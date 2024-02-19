<?php

declare(strict_types=1);

namespace Macpaw\SymfonyDeprecatedRoutes\Routing\AnnotationReader;

use Macpaw\SymfonyDeprecatedRoutes\Routing\Attribute\DeprecatedRoute;
use Macpaw\SymfonyDeprecatedRoutes\Routing\Exception\SinceHeaderRequiredException;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

final class MarkDeprecatedRoutes
{
    public const DEPRECATED_OPTION = 'DEPRECATED';
    public const DEPRECATED_SINCE_OPTION = 'DEPRECATED_SINCE';
    public const DEPRECATED_MESSAGE_OPTION = 'DEPRECATED_MESSAGE';
    public const DEPRECATED_FROM_OPTION = 'DEPRECATED_FROM';

    public function __construct(
        private readonly bool $sinceOptionIsRequired = false,
    ) {
    }

    /**
     * @throws ReflectionException
     */
    public function updateRoutes(RouteCollection $routes): void
    {
        foreach ($routes->getIterator() as $route) {
            $this->updateRoute($route);
        }
    }

    /**
     * @throws ReflectionException
     */
    private function updateRoute(Route $route): void
    {
        $controller = $route->getDefault('_controller');

        if (!is_string($controller)) {
            return;
        }

        if (
            !$this->isControllerClassWithMethod($controller) &&
            !$this->isInvokableController($controller)
        ) {
            return;
        }

        $controller = $this->parseController($controller);
        /** @var class-string $class */
        $class = $controller[0];

        if (null === ($attribute = $this->getDeprecatedAttribute($class, $controller[1] ?? null))) {
            return;
        }

        $attributeInstance = $attribute->newInstance();
        assert($attributeInstance instanceof DeprecatedRoute);

        $route->addDefaults([
            self::DEPRECATED_OPTION => $this->buildDeprecatedOption($attributeInstance),
        ]);
    }

    /**
     * @return array<string, string|null>
     */
    private function buildDeprecatedOption(DeprecatedRoute $attribute): array
    {
        if (null === $attribute->since && true === $this->sinceOptionIsRequired) {
            throw new SinceHeaderRequiredException();
        }

        return [
            self::DEPRECATED_MESSAGE_OPTION => $attribute->message,
            self::DEPRECATED_FROM_OPTION => $attribute->from->format('Y-m-d'),
            self::DEPRECATED_SINCE_OPTION => $attribute->since?->format('Y-m-d'),
        ];
    }

    private function isControllerClassWithMethod(string $controller): bool
    {
        $controllerParts = $this->parseController($controller);

        if (2 !== count($controllerParts) || !class_exists($controllerParts[0])) {
            return false;
        }

        $reflection = new ReflectionClass($controllerParts[0]);

        return $reflection->hasMethod($controllerParts[1]);
    }

    private function isInvokableController(string $controller): bool
    {
        $controllerParts = $this->parseController($controller);

        if (1 !== count($controllerParts) || !class_exists($controller)) {
            return false;
        }

        $reflection = new ReflectionClass($controller);

        return $reflection->hasMethod('__invoke');
    }

    /**
     * @return array<int, string>
     */
    private function parseController(string $controller): array
    {
        return explode('::', $controller);
    }

    /**
     * @param class-string $class
     *
     * @throws ReflectionException
     *
     * @return ReflectionAttribute<DeprecatedRoute>|null
     */
    private function getDeprecatedAttribute(string $class, ?string $method): ?ReflectionAttribute
    {
        $reflectionClass = new ReflectionClass($class);

        $attributes = $reflectionClass->getAttributes(DeprecatedRoute::class);

        if (0 !== count($attributes)) {
            return $attributes[0];
        }

        $reflectionMethod = $reflectionClass->getMethod(null === $method ? '__invoke' : $method);

        $attributes = $reflectionMethod->getAttributes(DeprecatedRoute::class);

        return 0 !== count($attributes) ? $attributes[0] : null;
    }
}
