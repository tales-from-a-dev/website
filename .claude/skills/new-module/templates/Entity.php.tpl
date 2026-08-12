<?php

declare(strict_types=1);

namespace App\{{Module}}\Domain\Entity;

use App\{{Module}}\Infrastructure\Repository\{{Module}}Repository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as Orm;
use Symfony\Component\Clock\Clock;

// entities stay non-final: Doctrine needs to proxy them
#[Orm\Entity(repositoryClass: {{Module}}Repository::class)]
class {{Module}}
{
    #[Orm\Id]
    #[Orm\GeneratedValue]
    #[Orm\Column]
    public ?int $id = null;

    #[Orm\Column(type: Types::STRING, length: 255)]
    public string $name;

    #[Orm\Column(type: Types::DATETIME_IMMUTABLE)]
    public \DateTimeImmutable $createdAt;

    public function __construct()
    {
        $this->createdAt = Clock::get()->now();
    }
}
