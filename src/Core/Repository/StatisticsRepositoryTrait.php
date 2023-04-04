<?php

declare(strict_types=1);

namespace App\Core\Repository;

use App\Domain\Statistics\Model\StatisticResult;

trait StatisticsRepositoryTrait
{
    /**
     * @param array{period: string, count: int} $rawResult
     *
     * @return array<StatisticResult>
     */
    private function getStatisticsResult(array $rawResult): array
    {
        $result = [];
        foreach ($rawResult as ['period' => $period, 'count' => $count]) {
            $result[] = new StatisticResult(
                period: $period,
                count: $count
            );
        }

        return $result;
    }
}
