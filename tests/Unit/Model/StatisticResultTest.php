<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model;

use App\Domain\Statistics\Model\StatisticResult;
use PHPUnit\Framework\TestCase;

final class StatisticResultTest extends TestCase
{
    public function testItCanInstantiateStatisticResult(): void
    {
        $statisticResult = new StatisticResult('2023-01-01 00:00:00', 10);

        self::assertSame('2023-01-01 00:00:00', $statisticResult->period);
        self::assertSame(10, $statisticResult->count);
    }
}
