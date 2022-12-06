<?php

declare(strict_types=1);

namespace Fixtures;

use App\Tests\Factory\ProjectFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class ProjectFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        ProjectFactory::new()
            ->asCustomerProject()
            ->many(20)
            ->create()
        ;

        ProjectFactory::new()
            ->asGitHubProject()
            ->many(10)
            ->create()
        ;
    }
}
