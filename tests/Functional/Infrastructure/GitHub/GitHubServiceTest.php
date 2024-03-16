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

        self::assertCount(6, $pinnedRepositories);
        self::assertArrayHasKey('id', $pinnedRepositories[0]);
        self::assertArrayHasKey('name', $pinnedRepositories[0]);
        self::assertArrayHasKey('description', $pinnedRepositories[0]);
        self::assertArrayHasKey('forkCount', $pinnedRepositories[0]);
        self::assertArrayHasKey('stargazerCount', $pinnedRepositories[0]);
        self::assertArrayHasKey('url', $pinnedRepositories[0]);
        self::assertArrayHasKey('languages', $pinnedRepositories[0]);
    }
}
