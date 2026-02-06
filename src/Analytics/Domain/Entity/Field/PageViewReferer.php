<?php

declare(strict_types=1);

namespace App\Analytics\Domain\Entity\Field;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as Orm;

#[Orm\Embeddable]
final class PageViewReferer
{
    #[Orm\Column(name: 'referer', type: Types::STRING, length: 255, nullable: true)]
    public readonly ?string $value;

    public function __construct(?string $value = null)
    {
        $this->value = $value;
    }
}
