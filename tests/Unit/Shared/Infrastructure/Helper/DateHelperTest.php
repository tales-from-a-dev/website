<?php

declare(strict_types=1);

namespace App\Tests\Unit\Shared\Infrastructure\Helper;

use App\Shared\Infrastructure\Helper\DateHelper;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Translation\LocaleSwitcher;

final class DateHelperTest extends TestCase
{
    public function testGetMonthsReturnsTranslatedMonths(): void
    {
        $localeSwitcher = $this->createMock(LocaleSwitcher::class);

        $localeSwitcher
            ->expects($this->once())
            ->method('getLocale')
            ->willReturn('en')
        ;

        $localeSwitcher
            ->expects($this->once())
            ->method('runWithLocale')
            ->with('en', $this->isCallable())
            ->willReturnCallback(static fn ($locale, $callback) => $callback());

        $dateHelper = new DateHelper($localeSwitcher);
        $months = $dateHelper->getMonths();

        $this->assertCount(12, $months);
        $this->assertSame('January', $months[1]);
        $this->assertSame('December', $months[12]);
    }

    public function testGetMonthsUsesCache(): void
    {
        $localeSwitcher = $this->createMock(LocaleSwitcher::class);

        $localeSwitcher
            ->expects($this->exactly(2))
            ->method('getLocale')
            ->willReturn('en')
        ;

        $localeSwitcher
            ->expects($this->once())
            ->method('runWithLocale')
            ->with('en', $this->isCallable())
            ->willReturnCallback(static fn ($locale, $callback) => $callback())
        ;

        $dateHelper = new DateHelper($localeSwitcher);
        $dateHelper->getMonths();
        $dateHelper->getMonths();
    }

    public function testGetMonthsWithSpecificLocale(): void
    {
        $localeSwitcher = $this->createMock(LocaleSwitcher::class);

        $localeSwitcher
            ->expects($this->never())
            ->method('getLocale');

        $localeSwitcher
            ->expects($this->once())
            ->method('runWithLocale')
            ->with('fr', $this->isCallable())
            ->willReturnCallback(static fn ($locale, $callback) => $callback());

        $dateHelper = new DateHelper($localeSwitcher);
        $months = $dateHelper->getMonths('fr');

        $this->assertCount(12, $months);
        $this->assertSame('janvier', mb_strtolower($months[1]));
    }
}
