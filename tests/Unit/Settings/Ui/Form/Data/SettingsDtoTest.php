<?php

declare(strict_types=1);

namespace App\Tests\Unit\Settings\Ui\Form\Data;

use App\Settings\Ui\Form\Data\SettingsDto;
use PHPUnit\Framework\TestCase;

final class SettingsDtoTest extends TestCase
{
    public function testItCanInstantiateDto(): void
    {
        $settings = new SettingsDto(true, new \DateTimeImmutable('today'), 500);

        self::assertTrue($settings->available);
        self::assertInstanceOf(\DateTimeImmutable::class, $settings->availableAt);
        self::assertSame(500, $settings->averageDailyRate);
    }
}
