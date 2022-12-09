<?php

declare(strict_types=1);

namespace App\Domain\Project;

use App\Domain\Project\Entity\Project;
use App\Domain\Project\Event\ProjectCreatedEvent;
use App\Domain\Project\Event\ProjectDeletedEvent;
use App\Domain\Project\Event\ProjectUpdatedEvent;
use App\Domain\Project\Repository\ProjectRepository;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final readonly class ProjectManager
{
    public function __construct(
        private ProjectRepository $repository,
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function getRepository(): ProjectRepository
    {
        return $this->repository;
    }

    public function create(Project $project): void
    {
        $this->repository->save($project, true);
        $this->eventDispatcher->dispatch(new ProjectCreatedEvent($project));
    }

    public function update(Project $project): void
    {
        $this->repository->save($project, true);
        $this->eventDispatcher->dispatch(new ProjectUpdatedEvent($project));
    }

    public function delete(Project $project): void
    {
        $this->repository->remove($project, true);
        $this->eventDispatcher->dispatch(new ProjectDeletedEvent($project));
    }
}
