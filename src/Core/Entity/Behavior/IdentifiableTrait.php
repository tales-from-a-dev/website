<?php

declare(strict_types=1);

namespace App\Core\Entity\Behavior;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

trait IdentifiableTrait
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(name: 'id', type: Types::INTEGER, options: ['unsigned' => true])]
    private ?int $id = null;

    public function getId(): ?int
    {
        return $this->id;
    }
}
