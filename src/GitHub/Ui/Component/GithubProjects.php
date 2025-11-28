<?php

declare(strict_types=1);

namespace App\GitHub\Ui\Component;

use App\GitHub\Domain\Service\GitHubServiceInterface;
use App\GitHub\Domain\ValueObject\GitHubProject;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final readonly class GithubProjects
{
    public function __construct(
        private GitHubServiceInterface $githubService,
    ) {
    }

    /**
     * @return GitHubProject[]
     */
    public function getProjects(): array
    {
        return $this->githubService->getPinnedRepositories();
    }
}
