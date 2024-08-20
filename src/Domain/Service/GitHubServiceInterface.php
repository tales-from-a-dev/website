<?php

declare(strict_types=1);

namespace App\Domain\Service;

interface GitHubServiceInterface
{
    /**
     * @return array<array{
     *     id: string,
     *     name: string,
     *     description: string,
     *     url: string,
     *     forkCount: int,
     *     stargazerCount: int,
     *     languages: array{
     *         nodes: array{
     *             0: array{
     *                 name: string
     *             }
     *         }
     *     }
     * }>
     */
    public function getPinnedRepositories(): array;
}
