<?php

declare(strict_types=1);

namespace Fixtures;

use App\Tests\Factory\TagFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class TagFixtures extends Fixture
{
    #[\Override]
    public function load(ObjectManager $manager): void
    {
        TagFactory::createMany(10);
    }
}
