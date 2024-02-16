<?php

declare(strict_types=1);

namespace Macpaw\SymfonyDeprecatedRoutes\Tests\App\Controller;

use DateTimeImmutable;
use Macpaw\SymfonyDeprecatedRoutes\Routing\Attribute\DeprecatedRoute;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route(path: '/', name: 'home')]
    #[DeprecatedRoute(
        message: 'Deprecated route',
        from: new DateTimeImmutable('2019-01-01'),
        since: new DateTimeImmutable('2020-01-01'),
    )]
    public function home(): JsonResponse
    {
        return $this->json([
            'status' => 'OK',
        ]);
    }

    #[Route(path: '/', name: 'home1')]
    #[DeprecatedRoute(
        message: 'Deprecated route',
        from: new DateTimeImmutable('2019-01-01'),
    )]
    public function home1(): JsonResponse
    {
        return $this->json([
            'status' => 'OK',
        ]);
    }

    #[Route(path: '/', name: 'home2')]
    #[DeprecatedRoute(
        message: 'Deprecated route',
        from: new DateTimeImmutable('2019-01-01'),
    )]
    public function __invoke(): JsonResponse
    {
        return $this->json([
            'status' => 'OK',
        ]);
    }
}
