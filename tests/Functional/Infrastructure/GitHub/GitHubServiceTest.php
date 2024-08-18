<?php

declare(strict_types=1);

namespace App\Tests\Functional\Infrastructure\GitHub;

use App\Infrastructure\GitHub\GitHubService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class GitHubServiceTest extends KernelTestCase
{
    public function testItCanFetchPinnedRepositories(): void
    {
        /** @var GitHubService $gitHubService */
        $gitHubService = static::getContainer()->get('app.github');

        self::assertInstanceOf(GitHubService::class, $gitHubService);

        $pinnedRepositories = $gitHubService->getPinnedRepositories();

        self::assertEmpty($pinnedRepositories);
    }
}
