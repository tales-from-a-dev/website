<?php

declare(strict_types=1);

namespace App\Domain\Statistics\Helper;

use App\Core\Helper\DateHelper;
use App\Domain\Statistics\Model\StatisticResult;

final class StatisticsHelper
{
    /**
     * @param array<StatisticResult> $results
     *
     * @return array<string, int|null>
     */
    public static function formatCountByMonth(array $results): array
    {
        $data = [];
        for ($i = 1; $i <= 12; ++$i) {
            $month = DateHelper::month($i);

            $data[$month] = null;
            foreach ($results as $result) {
                if ($i === (int) date('m', (int) strtotime($result->period))) {
                    $data[$month] = $result->count;
                }
            }
        }

        return $data;
    }
}
