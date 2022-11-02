<?php

declare(strict_types=1);

namespace App\Domain\Blog\Repository;

use App\Domain\Blog\Entity\Post;
use App\Domain\Blog\Enum\PublicationStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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

    public function queryAllPublished(): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('post');

        return $queryBuilder
            ->where(
                $queryBuilder->expr()->isNotNull('post.publishedAt'),
                $queryBuilder->expr()->lte('post.publishedAt', ':currentDate'),
                $queryBuilder->expr()->eq('post.publicationStatus', ':publicationStatus')
            )
            ->setParameters([
                'currentDate' => new \DateTimeImmutable(),
                'publicationStatus' => PublicationStatus::Published,
            ])
            ->orderBy('post.publishedAt', 'DESC')
        ;
    }
}
