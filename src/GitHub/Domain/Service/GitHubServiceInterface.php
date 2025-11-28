<?php

declare(strict_types=1);

namespace App\GitHub\Domain\Service;

use App\GitHub\Domain\ValueObject\GitHubProject;

interface GitHubServiceInterface
{
    /**
     * @return GitHubProject[]
     */
    public function getPinnedRepositories(): array;
}
