<?php

declare(strict_types=1);

namespace App\Tests\Unit\Helper;

use App\Core\Helper\DateHelper;
use PHPUnit\Framework\TestCase;

final class DateHelperTest extends TestCase
{
    public function testItCanReturnMonths(): void
    {
        $months = DateHelper::months();

        $this->assertSame(
            [
                1 => 'Janvier',
                2 => 'Février',
                3 => 'Mars',
                4 => 'Avril',
                5 => 'Mai',
                6 => 'Juin',
                7 => 'Juillet',
                8 => 'Août',
                9 => 'Septembre',
                10 => 'Octobre',
                11 => 'Novembre',
                12 => 'Décembre',
            ],
            $months
        );
    }

    public function testItCanReturnMonthsWithSpecificLocale(): void
    {
        $months = DateHelper::months('en');

        $this->assertSame(
            [
                1 => 'January',
                2 => 'February',
                3 => 'March',
                4 => 'April',
                5 => 'May',
                6 => 'June',
                7 => 'July',
                8 => 'August',
                9 => 'September',
                10 => 'October',
                11 => 'November',
                12 => 'December',
            ],
            $months
        );
    }

    public function testItCanReturnSpecificMonth(): void
    {
        $month = DateHelper::month(1);

        $this->assertSame('Janvier', $month);
    }

    public function testItCanReturnSpecificMonthWithSpecificLocale(): void
    {
        $month = DateHelper::month(1, 'en');

        $this->assertSame('January', $month);
    }
}
