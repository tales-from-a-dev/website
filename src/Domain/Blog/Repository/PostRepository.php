<?php

declare(strict_types=1);

namespace App\Domain\Blog\Repository;

use App\Core\Repository\StatisticsRepositoryInterface;
use App\Core\Repository\StatisticsRepositoryTrait;
use App\Domain\Blog\Entity\Post;
use App\Domain\Blog\Enum\PublicationStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Post>
 *
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository implements StatisticsRepositoryInterface
{
    use StatisticsRepositoryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    public function save(Post $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Post $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return array<Post>
     */
    public function findLatest(): array
    {
        return $this
            ->createQueryBuilder('p')
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery()
            ->setMaxResults(5)
            ->getResult()
        ;
    }

    public function queryAll(): Query
    {
        return $this
            ->createQueryBuilder('p')
            ->select('p', 't')
            ->leftJoin('p.tags', 't')
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery();
    }

    public function queryAllPublished(mixed $search): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('p');
        $queryBuilder
            ->select('p', 't')
            ->leftJoin('p.tags', 't')
            ->where(
                $queryBuilder->expr()->isNotNull('p.publishedAt'),
                $queryBuilder->expr()->lte('p.publishedAt', ':currentDate'),
                $queryBuilder->expr()->eq('p.publicationStatus', ':publicationStatus')
            )
            ->setParameters([
                'currentDate' => new \DateTimeImmutable(),
                'publicationStatus' => PublicationStatus::Published,
            ])
        ;

        if ('' !== $search) {
            $queryBuilder
                ->andWhere(
                    $queryBuilder->expr()->like('LOWER(p.title)', 'LOWER(:search)')
                )
                ->setParameter('search', "%{$search}%")
            ;
        }

        return $queryBuilder
            ->orderBy('p.publishedAt', 'DESC')
        ;
    }

    public function countByMonth(string $year = null): array
    {
        $year ??= date('Y');

        $query = <<<SQL
            SELECT DATE_TRUNC('month', p.published_at) as period, COUNT(*) as count
            FROM post AS p
            WHERE p.published_at IS NOT NULL
            AND EXTRACT(YEAR FROM p.published_at) = :year
            AND p.published_at <= CURRENT_DATE
            GROUP BY period
            ORDER BY period DESC
        SQL;

        /** @var array{period: string, count: int} $rawResult */
        $rawResult = $this->getEntityManager()->getConnection()
            ->prepare($query)
            ->executeQuery(['year' => $year])
            ->fetchAllAssociative();

        return $this->getStatisticsResult($rawResult);
    }
}
