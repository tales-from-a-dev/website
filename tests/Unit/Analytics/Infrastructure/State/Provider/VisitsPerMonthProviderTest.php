<?php

declare(strict_types=1);

namespace App\Tests\Unit\Analytics\Infrastructure\State\Provider;

use App\Analytics\Domain\Repository\PageViewRepositoryInterface;
use App\Analytics\Domain\ValueObject\Dataset;
use App\Analytics\Infrastructure\State\Provider\VisitsPerMonthProvider;
use App\Shared\Infrastructure\Helper\DateHelper;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Translation\LocaleSwitcher;

final class VisitsPerMonthProviderTest extends TestCase
{
    public function testProvideReturnsDatasetWithMappedData(): void
    {
        $pageViewRepository = $this->createMock(PageViewRepositoryInterface::class);
        $dateHelper = $this->createDateHelperMock();

        $visitsPerMonth = [
            ['period' => '2024-01-01', 'count' => 10],
            ['period' => '2024-03-15', 'count' => 25],
        ];

        $pageViewRepository
            ->expects($this->once())
            ->method('countByMonth')
            ->willReturn($visitsPerMonth);

        $months = [
            1 => 'january',
            2 => 'february',
            3 => 'march',
            4 => 'april',
        ];

        $dateHelper
            ->expects($this->once())
            ->method('getMonths')
            ->willReturn($months);

        $provider = new VisitsPerMonthProvider($pageViewRepository, $dateHelper);
        $dataset = $provider->provide();

        $this->assertInstanceOf(Dataset::class, $dataset);
        $this->assertSame(['January', 'February', 'March', 'April'], $dataset->labels);
        $this->assertSame([10, null, 25, null], $dataset->data);
    }

    private function createDateHelperMock(): DateHelper&MockObject
    {
        return $this->getMockBuilder(DateHelper::class)
            ->setConstructorArgs([$this->createStub(LocaleSwitcher::class)])
            ->onlyMethods(['getMonths'])
            ->getMock();
    }
}
