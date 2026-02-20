<?php

declare(strict_types=1);

namespace App\Shared\Domain\State;

/**
 * @template T of object
 */
interface ProviderInterface
{
    /**
     * @param array<string, mixed> $context
     *
     * @return T|iterable<T>|null
     */
    public function provide(array $context = []): object|array|null;
}
