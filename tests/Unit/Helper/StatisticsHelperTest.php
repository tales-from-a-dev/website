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
                'January' => null,
                'February' => null,
                'March' => null,
                'April' => null,
                'May' => null,
                'June' => null,
                'July' => null,
                'August' => null,
                'September' => null,
                'October' => null,
                'November' => null,
                'December' => null,
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
                'January' => 10,
                'February' => null,
                'March' => 24,
                'April' => null,
                'May' => null,
                'June' => null,
                'July' => 15,
                'August' => null,
                'September' => null,
                'October' => null,
                'November' => null,
                'December' => 1,
            ],
            $data
        );
    }
}
