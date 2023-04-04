<?php

declare(strict_types=1);

namespace App\Domain\Statistics\Model;

final readonly class StatisticResult
{
    public function __construct(
        public string $period,
        public int $count,
    ) {
    }
}
