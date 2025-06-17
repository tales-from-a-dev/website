<?php

declare(strict_types=1);

namespace App\Domain\Service;

use App\Domain\ValueObject\GitHubProject;

interface GitHubServiceInterface
{
    /**
     * @return GitHubProject[]
     */
    public function getPinnedRepositories(): array;
}
