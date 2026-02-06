<?php

declare(strict_types=1);

namespace App\Shared\Domain\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as Orm;
use Symfony\Component\Clock\Clock;

#[Orm\Embeddable]
final class CreatedAt
{
    #[Orm\Column(name: 'created_at', type: Types::DATETIME_IMMUTABLE)]
    public readonly \DateTimeImmutable $value;

    public function __construct(?\DateTimeImmutable $value = null)
    {
        $this->value = $value ?? Clock::get()->now();
    }
}
