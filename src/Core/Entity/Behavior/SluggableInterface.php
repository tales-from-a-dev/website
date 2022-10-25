<?php

declare(strict_types=1);

namespace App\Core\Entity\Behavior;

interface SluggableInterface
{
    /**
     * @return array<int, string>
     */
    public function getSluggableFields(): array;

    public function getSlug(): ?string;

    public function setSlug(): void;
}
