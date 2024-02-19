<?php

declare(strict_types=1);

namespace Macpaw\SymfonyDeprecatedRoutes\Tests\App;

use Macpaw\SymfonyDeprecatedRoutes\DeprecatedRoutesBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

final class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    public function registerBundles(): iterable
    {
        $bundles = [
            FrameworkBundle::class,
            DeprecatedRoutesBundle::class,
        ];
        foreach ($bundles as $bundle) {
            if (!class_exists($bundle)) {
                continue;
            }

            yield new $bundle();
        }
    }

    public function getConfigDir(): string
    {
        return __DIR__ . '/config';
    }
}
