<?php

declare(strict_types=1);

namespace Fixtures;

use App\Tests\Factory\PostFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class PostFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        PostFactory::new()
            ->withTags(1, 3)
            ->many(20)
            ->create()
        ;

        PostFactory::new()
            ->published()
            ->withTags(1, 3)
            ->many(10)
            ->create()
        ;

        PostFactory::new()
            ->publishedInFuture()
            ->withTags(1, 3)
            ->many(10)
            ->create()
        ;
    }

    public function getDependencies(): array
    {
        return [
            TagFixtures::class,
        ];
    }
}
