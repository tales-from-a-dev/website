<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Domain\Project\Entity\Project;
use App\Domain\Project\Enum\ProjectType;
use PHPUnit\Framework\TestCase;

final class ProjectTest extends TestCase
{
    public function testItCanInstantiateProject(): void
    {
        $project = new Project();
        $project->setTitle('Dummy project');
        $project->setSubTitle('Dummy project subtitle');
        $project->setDescription('Dummy project description');
        $project->setType(ProjectType::Customer);
        $project->setUrl('https://example.com');
        $project->setMetadata(['foo', 'bar']);
        $project->setSlug();
        $project->setCreatedAt();
        $project->setUpdatedAt();

        self::assertSame('Dummy project', $project->getTitle());
        self::assertSame('Dummy project subtitle', $project->getSubTitle());
        self::assertSame('Dummy project description', $project->getDescription());
        self::assertSame(ProjectType::Customer, $project->getType());
        self::assertSame('https://example.com', $project->getUrl());
        self::assertCount(2, $project->getMetadata());
        self::assertContains('foo', $project->getMetadata());
        self::assertContains('bar', $project->getMetadata());
        self::assertSame('dummy-project', $project->getSlug());
        self::assertNotNull($project->getCreatedAt());
        self::assertNotNull($project->getUpdatedAt());
    }
}
