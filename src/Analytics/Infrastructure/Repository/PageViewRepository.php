<?php

declare(strict_types=1);

namespace App\Analytics\Infrastructure\Repository;

use App\Analytics\Domain\Entity\PageView;
use App\Analytics\Domain\Repository\PageViewRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception;
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

    public function countByMonth(?string $year = null): array
    {
        return $this->countBy('month', $year);
    }

    public function countByDay(?string $year = null): array
    {
        return $this->countBy('day', $year);
    }

    /**
     * @return list<array{period: string, count: int}>
     */
    private function countBy(string $field, ?string $year = null): array
    {
        $year ??= date('Y');

        $connection = $this->getEntityManager()->getConnection();

        $query = $connection->createQueryBuilder();
        $query
            ->select(\sprintf("DATE_TRUNC('%s', pv.visited_at) as period", $field))
            ->addSelect('COUNT(pv.id) as count')
            ->from(
                $this->getEntityManager()->getClassMetadata(PageView::class)->getTableName()/* @type PageView */,
                'pv'
            )
            ->where('pv.visited_at IS NOT NULL')
            ->andWhere('EXTRACT(YEAR FROM pv.visited_at) = :year')
            ->andWhere('pv.visited_at <= CURRENT_DATE')
            ->groupBy('period')
            ->orderBy('period', 'DESC')
            ->setParameter('year', $year)
        ;

        try {
            /** @var list<array{period: string, count: int}> $result */
            $result = $connection
                ->executeQuery($query->getSQL(), $query->getParameters(), $query->getParameterTypes())
                ->fetchAllAssociative();

            return $result;
        } catch (Exception) {
            return [];
        }
    }
}
