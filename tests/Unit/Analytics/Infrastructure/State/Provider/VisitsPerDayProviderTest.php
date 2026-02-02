<?php

declare(strict_types=1);

namespace App\Tests\Unit\Analytics\Infrastructure\State\Provider;

use App\Analytics\Domain\Repository\PageViewRepositoryInterface;
use App\Analytics\Domain\ValueObject\Dataset;
use App\Analytics\Infrastructure\State\Provider\VisitsPerDayProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class VisitsPerDayProviderTest extends TestCase
{
    private PageViewRepositoryInterface&MockObject $pageViewRepository;

    private VisitsPerDayProvider $provider;

    protected function setUp(): void
    {
        $this->pageViewRepository = $this->createMock(PageViewRepositoryInterface::class);

        $this->provider = new VisitsPerDayProvider(
            pageViewRepository: $this->pageViewRepository
        );
    }

    public function testProvideReturnsDatasetWithMappedData(): void
    {
        // We use a fixed date to have predictable results (January 2024 has 31 days)
        $date = new \DateTime('2024-01-15');

        $visitsPerDay = [
            ['period' => '2024-01-01', 'count' => 10],
            ['period' => '2024-01-05', 'count' => 25],
            ['period' => '2024-01-31', 'count' => 5],
        ];

        $this->pageViewRepository
            ->expects($this->once())
            ->method('countByDay')
            ->with('01', '2024')
            ->willReturn($visitsPerDay);

        $dataset = $this->provider->provide(['date' => $date]);

        $this->assertInstanceOf(Dataset::class, $dataset);
        $this->assertCount(31, $dataset->labels);
        $this->assertCount(31, $dataset->data);
        $this->assertSame(range(1, 31), $dataset->labels);
        $this->assertSame(10, $dataset->data[0]); // Day 1
        $this->assertSame(25, $dataset->data[4]); // Day 5
        $this->assertSame(5, $dataset->data[30]); // Day 31
        $this->assertNull($dataset->data[1]); // Day 2
    }

    public function testProvideUsesCurrentDateWhenNoContextProvided(): void
    {
        $now = new \DateTime('today');

        $this->pageViewRepository
            ->expects($this->once())
            ->method('countByDay')
            ->with($now->format('m'), $now->format('Y'))
            ->willReturn([]);

        $dataset = $this->provider->provide();

        $this->assertCount((int) $now->format('t'), $dataset->labels);
    }

    public function testProvideHandlesInvalidPeriodInVisit(): void
    {
        $date = new \DateTime('2024-01-15');

        $visitsPerDay = [
            ['period' => 'invalid-date', 'count' => 10],
            ['period' => '2024-01-05', 'count' => 25],
        ];

        $this->pageViewRepository
            ->expects($this->once())
            ->method('countByDay')
            ->willReturn($visitsPerDay);

        $dataset = $this->provider->provide(['date' => $date]);

        // Day 5 should be 25, others should be null (including invalid one)
        $this->assertSame(25, $dataset->data[4]);

        // Ensure no other data is set
        $dataWithoutDay5 = $dataset->data;
        unset($dataWithoutDay5[4]);
        foreach ($dataWithoutDay5 as $val) {
            $this->assertNull($val);
        }
    }
}
