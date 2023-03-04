<?php

declare(strict_types=1);

namespace App\Core\Enum;

interface ColoreableEnumInterface
{
    /**
     * Gets the color representation of the value.
     */
    public function getColor(): string;
}
