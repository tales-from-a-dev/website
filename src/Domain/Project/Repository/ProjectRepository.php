<?php

declare(strict_types=1);

namespace App\Domain\Project\Repository;

use App\Core\Repository\StatisticsRepositoryInterface;
use App\Core\Repository\StatisticsRepositoryTrait;
use App\Domain\Project\Entity\Project;
use App\Domain\Project\Enum\ProjectType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Project>
 *
 * @method Project|null find($id, $lockMode = null, $lockVersion = null)
 * @method Project|null findOneBy(array $criteria, array $orderBy = null)
 * @method Project[]    findAll()
 * @method Project[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProjectRepository extends ServiceEntityRepository implements StatisticsRepositoryInterface
{
    use StatisticsRepositoryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Project::class);
    }

    public function save(Project $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Project $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function queryAll(): Query
    {
        return $this
            ->createQueryBuilder('p')
            ->select('p')
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery();
    }

    public function queryAllByType(ProjectType $type): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('p');

        return $queryBuilder
            ->where($queryBuilder->expr()->eq('p.type', ':type'))
            ->setParameter('type', $type);
    }

    /**
     * @return array<Project>
     */
    public function findLatest(?ProjectType $type = null): array
    {
        $queryBuilder = null !== $type
            ? $this->queryAllByType($type)
            : $this->createQueryBuilder('p');

        return $queryBuilder
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery()
            ->setMaxResults(5)
            ->getResult();
    }

    public function findOneByGithubId(string $id): ?Project
    {
        $rsm = $this->createResultSetMappingBuilder('p');

        $query = <<<SQL
            SELECT %s
            FROM project AS p
            WHERE p.type = :type
            AND p.metadata::jsonb->>'id' = :id
        SQL;
        $rawQuery = \sprintf($query, $rsm->generateSelectClause());

        $query = $this->getEntityManager()->createNativeQuery($rawQuery, $rsm);
        $query->setParameters([
            'type' => ProjectType::GitHub->value,
            'id' => $id,
        ]);

        return $query->getOneOrNullResult();
    }

    #[\Override]
    public function countByMonth(?string $year = null): array
    {
        $year ??= date('Y');

        $connection = $this->getEntityManager()->getConnection();

        $query = $connection->createQueryBuilder();
        $query
            ->select("DATE_TRUNC('month', p.created_at) as period")
            ->addSelect('COUNT(p.id) as count')
            ->from(
                $this->getEntityManager()->getClassMetadata(Project::class)->getTableName()/* @type Project */,
                'p'
            )
            ->where('EXTRACT(YEAR FROM p.created_at) = :year')
            ->andWhere('p.created_at <= CURRENT_DATE')
            ->groupBy('period')
            ->orderBy('period', 'DESC')
            ->setParameter('year', $year)
        ;

        /** @var array{period: string, count: int} $rawResult */
        $rawResult = $connection
            ->executeQuery($query->getSQL(), $query->getParameters(), $query->getParameterTypes())
            ->fetchAllAssociative()
        ;

        return $this->getStatisticsResult($rawResult);
    }
}
