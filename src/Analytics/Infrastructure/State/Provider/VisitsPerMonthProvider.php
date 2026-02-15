<?php

declare(strict_types=1);

namespace App\Analytics\Infrastructure\State\Provider;

use App\Analytics\Domain\Repository\PageViewRepositoryInterface;
use App\Analytics\Domain\ValueObject\Dataset;
use App\Shared\Domain\State\ProviderInterface;
use App\Shared\Infrastructure\Helper\DateHelper;

/**
 * @implements ProviderInterface<Dataset>
 */
final readonly class VisitsPerMonthProvider implements ProviderInterface
{
    public function __construct(
        private PageViewRepositoryInterface $pageViewRepository,
        private DateHelper $dateHelper,
    ) {
    }

    public function provide(array $context = []): Dataset
    {
        $visitsPerMonth = $this->pageViewRepository->countByMonth();

        $labels = $this->dateHelper->getMonths();
        $data = array_fill_keys(array_keys($labels), null);

        foreach ($visitsPerMonth as $visit) {
            $timestamp = strtotime($visit['period']);
            if (false === $timestamp) {
                continue;
            }

            $monthNumber = (int) date('n', $timestamp);
            if (\array_key_exists($monthNumber, $data)) {
                $data[$monthNumber] = $visit['count'];
            }
        }

        return new Dataset(
            labels: array_values(array_map(ucfirst(...), $labels)),
            data: array_values($data),
        );
    }
}
