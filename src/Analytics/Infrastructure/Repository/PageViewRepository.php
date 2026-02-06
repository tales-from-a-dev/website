<?php

declare(strict_types=1);

namespace App\Analytics\Infrastructure\Repository;

use App\Analytics\Domain\Entity\PageView;
use App\Analytics\Domain\Repository\PageViewRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PageView>
 */
final class PageViewRepository extends ServiceEntityRepository implements PageViewRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PageView::class);
    }

    public function add(PageView $pageView): void
    {
        $this->getEntityManager()->persist($pageView);
    }
}
