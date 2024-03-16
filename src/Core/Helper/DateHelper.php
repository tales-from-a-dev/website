<?php

declare(strict_types=1);

namespace App\Core\Helper;

final class DateHelper
{
    /**
     * @return array<int, string>
     */
    public static function months(?string $locale = null): array
    {
        $dateFormatter = new \IntlDateFormatter(locale: $locale, pattern: 'MMMM');

        $months = [];
        for ($i = 1; $i <= 12; ++$i) {
            if ($dateTime = \DateTimeImmutable::createFromFormat('!m', (string) $i)) {
                $months[$i] = ucfirst((string) $dateFormatter->format($dateTime));
            }
        }

        return $months;
    }

    public static function month(int $month, ?string $locale = null): string
    {
        return static::months($locale)[$month];
    }
}
