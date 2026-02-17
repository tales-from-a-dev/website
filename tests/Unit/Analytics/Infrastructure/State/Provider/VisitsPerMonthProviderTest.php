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
    private PageViewRepositoryInterface&MockObject $pageViewRepository;
    private DateHelper&MockObject $dateHelper;

    private VisitsPerMonthProvider $provider;

    protected function setUp(): void
    {
        $this->pageViewRepository = $this->createMock(PageViewRepositoryInterface::class);
        $this->dateHelper = $this->createDateHelperMock();

        $this->provider = new VisitsPerMonthProvider(
            pageViewRepository: $this->pageViewRepository,
            dateHelper: $this->dateHelper
        );
    }

    public function testProvideReturnsDatasetWithMappedData(): void
    {
        $visitsPerMonth = [
            ['period' => '2024-01-01', 'count' => 10],
            ['period' => '2024-03-15', 'count' => 25],
        ];

        $this->pageViewRepository
            ->expects($this->once())
            ->method('countByMonth')
            ->willReturn($visitsPerMonth);

        $months = [
            1 => 'january',
            2 => 'february',
            3 => 'march',
            4 => 'april',
        ];

        $this->dateHelper
            ->expects($this->once())
            ->method('getMonths')
            ->willReturn($months);

        $dataset = $this->provider->provide();

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
