<?php

declare(strict_types=1);

namespace App\Tests\Unit\Helper;

use App\Domain\Statistics\Helper\StatisticsHelper;
use App\Domain\Statistics\Model\StatisticResult;
use PHPUnit\Framework\TestCase;

final class StatisticsHelperTest extends TestCase
{
    public function testItCanReturnArrayOfNull(): void
    {
        $data = StatisticsHelper::formatCountByMonth([]);

        $this->assertSame(
            [
                'Janvier' => null,
                'Février' => null,
                'Mars' => null,
                'Avril' => null,
                'Mai' => null,
                'Juin' => null,
                'Juillet' => null,
                'Août' => null,
                'Septembre' => null,
                'Octobre' => null,
                'Novembre' => null,
                'Décembre' => null,
            ],
            $data
        );
    }

    public function testItCanReturnArrayOfMixed(): void
    {
        $data = StatisticsHelper::formatCountByMonth([
            new StatisticResult('2023-01-01 00:00:00', 10),
            new StatisticResult('2023-03-01 00:00:00', 24),
            new StatisticResult('2023-07-01 00:00:00', 15),
            new StatisticResult('2023-12-01 00:00:00', 1),
        ]);

        $this->assertSame(
            [
                'Janvier' => 10,
                'Février' => null,
                'Mars' => 24,
                'Avril' => null,
                'Mai' => null,
                'Juin' => null,
                'Juillet' => 15,
                'Août' => null,
                'Septembre' => null,
                'Octobre' => null,
                'Novembre' => null,
                'Décembre' => 1,
            ],
            $data
        );
    }
}
