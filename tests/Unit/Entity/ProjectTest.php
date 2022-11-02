<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Domain\Project\Entity\Project;
use App\Domain\Project\Enum\ProjectType;
use App\Domain\Project\Model\GitHubProject;
use PHPUnit\Framework\TestCase;

final class ProjectTest extends TestCase
{
    public function testItCanInstantiateProject(): void
    {
        $project = new Project();
        $project->setTitle('Dummy project');
        $project->setSubTitle('Dummy project subtitle');
        $project->setDescription('Dummy project description');
        $project->setType(ProjectType::GitHub);
        $project->setUrl('https://example.com');
        $project->setSlug();
        $project->setCreatedAt();
        $project->setUpdatedAt();

        self::assertSame('Dummy project', $project->getTitle());
        self::assertSame('Dummy project subtitle', $project->getSubTitle());
        self::assertSame('Dummy project description', $project->getDescription());
        self::assertSame(ProjectType::GitHub, $project->getType());
        self::assertSame('https://example.com', $project->getUrl());
        self::assertNull($project->getMetadata());
        self::assertSame('dummy-project', $project->getSlug());
        self::assertNotNull($project->getCreatedAt());
        self::assertNotNull($project->getUpdatedAt());
    }

    public function testItCanCastProjectToString(): void
    {
        $project = new Project();
        $project->setTitle('Dummy project');

        self::assertSame('Dummy project', (string) $project);
    }

    public function testItCanInstantiateProjectWithGithubMetadata(): void
    {
        $project = new Project();
        $project->setMetadata(new GitHubProject(
            'dummy-github-project-id',
            10,
            10,
            ['php'],
        ));

        self::assertNotNull($project->getMetadata());
        self::assertInstanceOf(GitHubProject::class, $project->getMetadata());

        /** @var GitHubProject $metadata */
        $metadata = $project->getMetadata();

        self::assertSame('dummy-github-project-id', $metadata->id);
        self::assertSame(10, $metadata->forkCount);
        self::assertSame(10, $metadata->stargazerCount);
        self::assertCount(1, $metadata->languages);
        self::assertContains('php', $metadata->languages);
    }
}
