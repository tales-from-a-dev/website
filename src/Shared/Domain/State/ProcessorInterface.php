<?php

declare(strict_types=1);

namespace App\Shared\Domain\State;

/**
 * @template TData
 * @template TReturn
 */
interface ProcessorInterface
{
    /**
     * @param TData                $data
     * @param array<string, mixed> $context
     *
     * @return TReturn
     */
    public function process(mixed $data, array $context = []);
}
