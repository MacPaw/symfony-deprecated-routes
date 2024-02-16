<?php

declare(strict_types=1);

namespace Macpaw\SymfonyDeprecatedRoutes\Tests\Functional;

use Macpaw\SymfonyDeprecatedRoutes\Routing\EventSubscriber\DeprecationRoutesEventSubscriber;
use Macpaw\SymfonyDeprecatedRoutes\Tests\App\Kernel;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelInterface;

final class DeprecatedRouteTest extends WebTestCase
{
    /**
     * @param array<int|string, mixed> $options
     */
    protected static function bootKernel(array $options = []): KernelInterface
    {
        static::$class = Kernel::class;

        return parent::bootKernel($options);
    }

    public function testDeprecatedRouteHeadersSet(): void
    {
        putenv('DISABLE_DEPRECATION_ROUTES=false');
        $client = $this->createClient();
        $client->insulate(false);
        $client->request(
            Request::METHOD_GET,
            '/',
            server: ['DISABLE_DEPRECATION_ROUTES' => false],
        );

        $response = $client->getResponse();
        /** @var string $content */
        $content = $response->getContent();
        $json = json_decode($content, true);

        self::assertIsArray($json);
        self::assertArrayHasKey('status', $json);
        self::assertEquals('OK', $json['status']);

        self::assertTrue(
            $response->headers->has(
                DeprecationRoutesEventSubscriber::X_DEPRECATED_HEADER,
            ),
        );
        self::assertEquals(
            'Deprecated route',
            $response->headers->get(
                DeprecationRoutesEventSubscriber::X_DEPRECATED_HEADER,
            ),
        );

        self::assertTrue(
            $response->headers->has(
                DeprecationRoutesEventSubscriber::X_DEPRECATED_SINCE_HEADER,
            ),
        );
        self::assertEquals(
            '2020-01-01',
            $response->headers->get(
                DeprecationRoutesEventSubscriber::X_DEPRECATED_SINCE_HEADER,
            ),
        );

        self::assertTrue(
            $response->headers->has(
                DeprecationRoutesEventSubscriber::X_DEPRECATED_FROM_HEADER,
            ),
        );
        self::assertEquals(
            '2019-01-01',
            $response->headers->get(
                DeprecationRoutesEventSubscriber::X_DEPRECATED_FROM_HEADER,
            ),
        );
    }

    public function testDeprecatedRouteHeadersNotSetWhenOptionIsDisabled(): void
    {
        putenv('DISABLE_DEPRECATION_ROUTES=true');
        $client = $this->createClient();
        $client->insulate(false);
        $client->request(
            Request::METHOD_GET,
            '/',
            server: ['DISABLE_DEPRECATION_ROUTES' => true]
        );


        $response = $client->getResponse();
        /** @var string $content */
        $content = $response->getContent();
        $json = json_decode($content, true);

        self::assertIsArray($json);
        self::assertArrayHasKey('status', $json);
        self::assertEquals('OK', $json['status']);

        self::assertFalse(
            $response->headers->has(
                DeprecationRoutesEventSubscriber::X_DEPRECATED_HEADER,
            ),
        );

        self::assertFalse(
            $response->headers->has(
                DeprecationRoutesEventSubscriber::X_DEPRECATED_SINCE_HEADER,
            ),
        );

        self::assertFalse(
            $response->headers->has(
                DeprecationRoutesEventSubscriber::X_DEPRECATED_FROM_HEADER,
            ),
        );
    }
}
