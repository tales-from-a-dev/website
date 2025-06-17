<?php

declare(strict_types=1);

namespace App\Ui\Component;

use App\Domain\Service\GitHubServiceInterface;
use App\Domain\ValueObject\GitHubProject;
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
