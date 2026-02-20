<?php

declare(strict_types=1);

namespace App\Analytics\Domain\Repository;

use App\Analytics\Domain\Entity\PageView;

interface PageViewRepositoryInterface
{
    public function add(PageView $pageView): void;

    /**
     * @return PageView[]
     */
    public function findLatest(int $limit): array;

    /**
     * @return list<array{period: string, count: int}>
     */
    public function countByMonth(?string $year = null): array;

    /**
     * @return list<array{period: string, count: int}>
     */
    public function countByDay(?string $month = null, ?string $year = null): array;
}
