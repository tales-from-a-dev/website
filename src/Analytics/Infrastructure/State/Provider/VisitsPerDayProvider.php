<?php

declare(strict_types=1);

namespace App\Analytics\Infrastructure\State\Provider;

use App\Analytics\Domain\Repository\PageViewRepositoryInterface;
use App\Analytics\Domain\ValueObject\Dataset;
use App\Shared\Domain\State\ProviderInterface;

/**
 * @implements ProviderInterface<Dataset>
 */
final readonly class VisitsPerDayProvider implements ProviderInterface
{
    public function __construct(
        private PageViewRepositoryInterface $pageViewRepository,
    ) {
    }

    public function provide(array $context = []): Dataset
    {
        $date = $context['date'] ?? new \DateTime('today');

        $visitsPerDay = $this->pageViewRepository->countByDay(
            month: $date->format('m'),
            year: $date->format('Y'),
        );

        $labels = range(1, (int) $date->format('t'));
        $data = array_fill_keys($labels, null);

        foreach ($visitsPerDay as $visit) {
            $timestamp = strtotime($visit['period']);
            if (false === $timestamp) {
                continue;
            }

            $dayNumber = (int) date('j', $timestamp);
            if (\array_key_exists($dayNumber, $data)) {
                $data[$dayNumber] = $visit['count'];
            }
        }

        return new Dataset(
            labels: $labels,
            data: array_values($data),
        );
    }
}
