<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\ValueObject;

use App\Domain\ValueObject\GitHubProject;
use PHPUnit\Framework\TestCase;

final class GithubProjectTest extends TestCase
{
    public function testItCanInstantiateGithubProject(): void
    {
        $gitHubProject = new GitHubProject('1', 'foo', 'foo bar', 10, 10, 'https://github.com/foo/bar', [['name' => 'php', 'color' => '#000']]);

        self::assertSame('1', $gitHubProject->id);
        self::assertSame('foo', $gitHubProject->name);
        self::assertSame('foo bar', $gitHubProject->description);
        self::assertSame(10, $gitHubProject->forkCount);
        self::assertSame(10, $gitHubProject->stargazerCount);
        self::assertNotEmpty($gitHubProject->languages);
        self::assertContains(['name' => 'php', 'color' => '#000'], $gitHubProject->languages);
    }
}
