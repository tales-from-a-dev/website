<?php

declare(strict_types=1);

namespace App\Shared\Domain\State;

use Symfony\Component\HttpFoundation\Request;

/**
 * @template T of object
 */
interface ProviderInterface
{
    /**
     * @param array<string, mixed>|array{request?: Request, entity?: string, pagination?: array{page?: int, itemsPerPage?: int}} $context
     *
     * @return T|null
     */
    public function provide(array $context = []): ?object;
}
