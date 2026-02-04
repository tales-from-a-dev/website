<?php

declare(strict_types=1);

namespace App\Shared\Domain\Entity;

use Doctrine\ORM\Mapping as Orm;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\AbstractUid;
use Symfony\Component\Uid\Uuid;

trait AggregateRootId
{
    #[Orm\Id]
    #[Orm\Column(name: 'id', type: UuidType::NAME)]
    public readonly AbstractUid $value;

    final public function __construct(?AbstractUid $value = null)
    {
        $this->value = $value ?? Uuid::v7();
    }

    public function __toString(): string
    {
        return $this->value->toString();
    }
}
