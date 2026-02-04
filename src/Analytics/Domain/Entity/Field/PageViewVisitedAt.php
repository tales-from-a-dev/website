<?php

declare(strict_types=1);

namespace App\Analytics\Domain\Entity\Field;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as Orm;

#[Orm\Embeddable]
final class PageViewVisitedAt
{
    #[Orm\Column(name: 'visited_at', type: Types::DATETIME_IMMUTABLE)]
    public readonly \DateTimeImmutable $value;

    public function __construct(\DateTimeImmutable $value)
    {
        $this->value = $value;
    }
}
