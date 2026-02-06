<?php

declare(strict_types=1);

namespace App\Analytics\Domain\Repository;

use App\Analytics\Domain\Entity\PageView;

interface PageViewRepositoryInterface
{
    public function add(PageView $pageView): void;
}
