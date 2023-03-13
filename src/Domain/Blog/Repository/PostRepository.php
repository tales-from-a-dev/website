<?php

declare(strict_types=1);

namespace App\Domain\Blog\Repository;

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
class PostRepository extends ServiceEntityRepository
{
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
}
