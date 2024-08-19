<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Model;

use App\Domain\Model\GitHubProject;
use PHPUnit\Framework\TestCase;

final class GithubProjectTest extends TestCase
{
    public function testItCanInstantiateGithubProject(): void
    {
        $gitHubProject = new GitHubProject('foo', 10, 10, ['php']);

        self::assertSame('foo', $gitHubProject->id);
        self::assertSame(10, $gitHubProject->forkCount);
        self::assertSame(10, $gitHubProject->stargazerCount);
        self::assertNotEmpty($gitHubProject->languages);
        self::assertContains('php', $gitHubProject->languages);
    }
}
