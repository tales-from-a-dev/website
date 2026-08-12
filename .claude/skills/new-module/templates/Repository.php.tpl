<?php

declare(strict_types=1);

namespace App\{{Module}}\Infrastructure\Repository;

use App\{{Module}}\Domain\Entity\{{Module}};
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<{{Module}}>
 */
final class {{Module}}Repository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, {{Module}}::class);
    }
}
