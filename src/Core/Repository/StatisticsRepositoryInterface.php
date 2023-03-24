<?php

declare(strict_types=1);

namespace App\Core\Repository;

use App\Domain\Statistics\Model\StatisticResult;

interface StatisticsRepositoryInterface
{
    /**
     * @return array<StatisticResult>
     */
    public function countByMonth(string $year = null): array;
}
