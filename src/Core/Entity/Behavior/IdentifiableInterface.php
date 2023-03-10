<?php

declare(strict_types=1);

namespace App\Core\Entity\Behavior;

interface IdentifiableInterface
{
    public function getId(): ?int;

    public function getEntityName(): string;
}
