<?php

declare(strict_types=1);

namespace Macpaw\SymfonyDeprecatedRoutes\Tests\Unit;

use Macpaw\SymfonyDeprecatedRoutes\Routing\AnnotationReader\MarkDeprecatedRoutes;
use Macpaw\SymfonyDeprecatedRoutes\Routing\EventSubscriber\DeprecationRoutesEventSubscriber;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class DeprecationRouteSubscriberTest extends TestCase
{
    public function testOnKernelResponse(): void
    {
        $deprecationHeaderName = 'X-DEPRECATED-TEST';
        $deprecationFromHeaderName = 'X-DEPRECATED-FROM-TEST';
        $deprecationSinceHeaderName = 'X-DEPRECATED-SINCE-TEST';

        $subscriber = new DeprecationRoutesEventSubscriber(
            $deprecationHeaderName,
            $deprecationFromHeaderName,
            $deprecationSinceHeaderName
        );

        $request = new Request();
        $request->attributes->set(
            MarkDeprecatedRoutes::DEPRECATED_OPTION,
            [
                MarkDeprecatedRoutes::DEPRECATED_MESSAGE_OPTION => 'Deprecated',
                MarkDeprecatedRoutes::DEPRECATED_FROM_OPTION => '1.0.0',
                MarkDeprecatedRoutes::DEPRECATED_SINCE_OPTION => '2.0.0',
            ]
        );

        $response = new Response();
        $event = new ResponseEvent(
            $this->createMock('Symfony\Component\HttpKernel\HttpKernelInterface'),
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            $response
        );

        $subscriber->onKernelResponse($event);

        $this->assertTrue($response->headers->has($deprecationHeaderName));
        $this->assertTrue($response->headers->has($deprecationFromHeaderName));
        $this->assertTrue($response->headers->has($deprecationSinceHeaderName));
        self::assertIsArray($subscriber::getSubscribedEvents());
    }
}
