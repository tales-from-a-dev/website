<?php

declare(strict_types=1);

namespace App\Domain\Project\Event;

use App\Domain\Project\Entity\Project;
use Symfony\Contracts\EventDispatcher\Event;

final class ProjectCreatedEvent extends Event
{
    public const string NAME = 'project.created';

    public function __construct(
        private readonly Project $project
    ) {
    }

    public function getProject(): Project
    {
        return $this->project;
    }
}
