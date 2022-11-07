<?php

declare(strict_types=1);

namespace Fixtures;

use App\Domain\Blog\Factory\PostFactory;
use App\Domain\Blog\Factory\TagFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class BlogFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        TagFactory::createMany(10);

        PostFactory::new()
            ->withTags(1, 3)
            ->many(20)
            ->create()
        ;

        PostFactory::new()
            ->publishedInFuture()
            ->many(10)
            ->create()
        ;
    }
}
