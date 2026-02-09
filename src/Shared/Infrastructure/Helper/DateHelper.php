<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Helper;

use Symfony\Component\Translation\LocaleSwitcher;

class DateHelper
{
    /**
     * @var array<string, array<int, mixed>>
     */
    private array $cache = [];

    public function __construct(
        private readonly LocaleSwitcher $localeSwitcher,
    ) {
    }

    /**
     * @return string[]
     */
    public function getMonths(?string $locale = null): array
    {
        $locale ??= $this->localeSwitcher->getLocale();
        $cacheKey = 'months_'.$locale;

        if (isset($this->cache[$cacheKey])) {
            return $this->cache[$cacheKey];
        }

        return $this->cache[$cacheKey] = $this->localeSwitcher->runWithLocale(
            $locale,
            fn () => $this->generateMonths($locale),
        );
    }

    /**
     * @return array<int, mixed>
     */
    private function generateMonths(string $locale): array
    {
        $formatter = new \IntlDateFormatter(
            locale: $locale,
            dateType: \IntlDateFormatter::NONE,
            timeType: \IntlDateFormatter::NONE,
            pattern: 'LLLL',
        );

        return array_map(static function ($timestamp) use ($formatter) {
            return $formatter->format($timestamp);
        }, $this->listMonths());
    }

    /**
     * @return int[]
     */
    private function listMonths(): array
    {
        $result = [];

        foreach (range(1, 12) as $month) {
            if (false !== $timestamp = gmmktime(0, 0, 0, $month, 15)) {
                $result[$month] = $timestamp;
            }
        }

        return $result;
    }
}
