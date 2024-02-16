<?php

declare(strict_types=1);

namespace Macpaw\SymfonyDeprecatedRoutes\Routing\EventSubscriber;

use Macpaw\SymfonyDeprecatedRoutes\Routing\AnnotationReader\MarkDeprecatedRoutes;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class DeprecationRoutesEventSubscriber implements EventSubscriberInterface
{
    public const X_DEPRECATED_HEADER = 'X-DEPRECATED';
    public const X_DEPRECATED_FROM_HEADER = 'X-DEPRECATED-FROM';
    public const X_DEPRECATED_SINCE_HEADER = 'X-DEPRECATED-SINCE';

    public function __construct(
        private readonly string $deprecationHeaderName,
        private readonly string $deprecationFromHeaderName,
        private readonly string $deprecationSinceHeaderName,
        private readonly bool $isDisabled = false,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::RESPONSE => ['onKernelResponse', 200],
        ];
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        $request = $event->getRequest();

        if (
            $this->isDisabled ||
            !$request->attributes->has(MarkDeprecatedRoutes::DEPRECATED_OPTION)
        ) {
            return;
        }

        /** @var array<string, string> $deprecatedOption */
        $deprecatedOption = $request->attributes->get(MarkDeprecatedRoutes::DEPRECATED_OPTION);

        $event->getResponse()->headers->add($this->buildDeprecatedHeaders($deprecatedOption));
    }

    /**
     * @param array<string, string> $deprecatedOption
     *
     * @return array<string, string>
     */
    private function buildDeprecatedHeaders(array $deprecatedOption): array
    {
        $headers = [
            $this->deprecationHeaderName => $deprecatedOption[MarkDeprecatedRoutes::DEPRECATED_MESSAGE_OPTION],
            $this->deprecationFromHeaderName => $deprecatedOption[MarkDeprecatedRoutes::DEPRECATED_FROM_OPTION],
        ];

        $sinceOptName = MarkDeprecatedRoutes::DEPRECATED_SINCE_OPTION;
        if (isset($deprecatedOption[$sinceOptName])) {
            $headers[$this->deprecationSinceHeaderName] = $deprecatedOption[$sinceOptName];
        }

        return $headers;
    }
}
