<?php

declare(strict_types=1);

namespace App\Analytics\Domain\ValueObject;

final class Dataset
{
    /**
     * @param string[]       $labels
     * @param list<int|null> $data
     */
    public function __construct(
        public array $labels,
        public array $data,
    ) {
    }
}
