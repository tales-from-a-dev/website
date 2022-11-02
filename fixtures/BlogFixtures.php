<?php

declare(strict_types=1);

namespace Fixtures;

use App\Domain\Blog\Factory\PostFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class BlogFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        PostFactory::createMany(30);

        PostFactory::new()
            ->publishedInFuture()
            ->many(10);
    }
}
