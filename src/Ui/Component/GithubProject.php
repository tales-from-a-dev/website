<?php

declare(strict_types=1);

namespace App\Ui\Component;

use App\Domain\Service\GitHubServiceInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final readonly class GithubProject
{
    public function __construct(
        private GitHubServiceInterface $githubService,
    ) {
    }

    public function getProjects(): array
    {
        return $this->githubService->getPinnedRepositories();
    }
}
