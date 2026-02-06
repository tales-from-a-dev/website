<?php

declare(strict_types=1);

namespace App\Analytics\Domain\Entity\Field;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as Orm;

#[Orm\Embeddable]
final class PageViewServer
{
    #[Orm\Column(name: 'server', type: Types::STRING, length: 255)]
    public readonly string $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }
}
