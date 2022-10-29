<?php

declare(strict_types=1);

namespace App\Tests\Functional\Domain\Project;

use App\Domain\Project\Entity\Project;
use App\Domain\Project\Factory\ProjectFactory;
use App\Domain\Project\ProjectManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;

final class ProjectManagerTest extends KernelTestCase
{
    use Factories;

    private ?ProjectManager $projectManager;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->projectManager = static::getContainer()->get(ProjectManager::class);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->projectManager = null;
    }

    public function testItCanCreateProject(): void
    {
        $proxy = ProjectFactory::new()
            ->withoutPersisting()
            ->create()
        ;

        self::ensureKernelShutdown();

        $this->projectManager->create($proxy->object());

        $entity = $this->projectManager->getRepository()->find($proxy->getId());

        self::assertNotNull($entity);
        self::assertInstanceOf(Project::class, $entity);
        self::assertSame($proxy->getId(), $entity->getId());
        self::assertSame($proxy->getTitle(), $entity->getTitle());
        self::assertSame($proxy->getSubTitle(), $entity->getSubTitle());
        self::assertSame($proxy->getDescription(), $entity->getDescription());
        self::assertSame($proxy->getMetadata(), $entity->getMetadata());
        self::assertSame($proxy->getSlug(), $entity->getSlug());
        self::assertSame($proxy->getCreatedAt(), $entity->getCreatedAt());
        self::assertSame($proxy->getUpdatedAt(), $entity->getUpdatedAt());
    }

    public function testItCanUpdateProject(): void
    {
        $proxy = ProjectFactory::createOne();

        self::ensureKernelShutdown();

        $entity = $proxy->object()->setTitle('Dummy update title');

        $this->projectManager->update($entity);

        $updatedEntity = $this->projectManager->getRepository()->find($entity->getId());

        self::assertNotNull($updatedEntity);
        self::assertSame('Dummy update title', $updatedEntity->getTitle());
        self::assertSame('dummy-update-title', $updatedEntity->getSlug());
    }

    public function testItCanDeleteProject(): void
    {
        $entity = new Project();
        $entity->setTitle('Dummy project');
        $entity->setDescription('Dummy description');
        $entity->setUrl('https://example.com');

        $this->projectManager->create($entity);

        $createdEntity = $this->projectManager->getRepository()->find($entity->getId());
        $createdEntityId = $createdEntity->getId();

        self::assertNotNull($createdEntity);

        $this->projectManager->delete($createdEntity);

        $deletedEntity = $this->projectManager->getRepository()->find($createdEntityId);

        self::assertNull($entity->getId());
        self::assertNull($deletedEntity);
    }

    public function testItCanFindProjectByGithubId(): void
    {
        $proxy = ProjectFactory::new()
            ->asGitHubProject()
            ->create()
        ;

        self::ensureKernelShutdown();

        $entity = $this->projectManager->getRepository()->findOneByGithubId($proxy->getMetadata()->id);

        self::assertNotNull($entity);
        self::assertInstanceOf(Project::class, $entity);
    }
}
