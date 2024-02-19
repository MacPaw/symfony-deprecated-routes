<?php

declare(strict_types=1);

namespace Macpaw\SymfonyDeprecatedRoutes\Routing\Attribute;

use Attribute;
use DateTimeInterface;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
final class DeprecatedRoute
{
    public function __construct(
        public string $message,
        public DateTimeInterface $from,
        public ?DateTimeInterface $since = null,
    ) {
    }
}
