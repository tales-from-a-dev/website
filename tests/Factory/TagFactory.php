<?php

declare(strict_types=1);

namespace App\Tests\Factory;

use App\Domain\Blog\Entity\Tag;

/**
 * @method        \App\Domain\Blog\Entity\Tag|\Zenstruck\Foundry\Persistence\Proxy                                                                create(array|callable $attributes = [])
 * @method static \App\Domain\Blog\Entity\Tag|\Zenstruck\Foundry\Persistence\Proxy                                                                createOne(array $attributes = [])
 * @method static \App\Domain\Blog\Entity\Tag|\Zenstruck\Foundry\Persistence\Proxy                                                                find(object|array|mixed $criteria)
 * @method static \App\Domain\Blog\Entity\Tag|\Zenstruck\Foundry\Persistence\Proxy                                                                findOrCreate(array $attributes)
 * @method static \App\Domain\Blog\Entity\Tag|\Zenstruck\Foundry\Persistence\Proxy                                                                first(string $sortedField = 'id')
 * @method static \App\Domain\Blog\Entity\Tag|\Zenstruck\Foundry\Persistence\Proxy                                                                last(string $sortedField = 'id')
 * @method static \App\Domain\Blog\Entity\Tag|\Zenstruck\Foundry\Persistence\Proxy                                                                random(array $attributes = [])
 * @method static \App\Domain\Blog\Entity\Tag|\Zenstruck\Foundry\Persistence\Proxy                                                                randomOrCreate(array $attributes = [])
 * @method static \App\Domain\Blog\Entity\Tag[]|\Zenstruck\Foundry\Persistence\Proxy[]                                                            all()
 * @method static \App\Domain\Blog\Entity\Tag[]|\Zenstruck\Foundry\Persistence\Proxy[]                                                            createMany(int $number, array|callable $attributes = [])
 * @method static \App\Domain\Blog\Entity\Tag[]|\Zenstruck\Foundry\Persistence\Proxy[]                                                            createSequence(iterable|callable $sequence)
 * @method static \App\Domain\Blog\Entity\Tag[]|\Zenstruck\Foundry\Persistence\Proxy[]                                                            findBy(array $attributes)
 * @method static \App\Domain\Blog\Entity\Tag[]|\Zenstruck\Foundry\Persistence\Proxy[]                                                            randomRange(int $min, int $max, array $attributes = [])
 * @method static \App\Domain\Blog\Entity\Tag[]|\Zenstruck\Foundry\Persistence\Proxy[]                                                            randomSet(int $number, array $attributes = [])
 * @method        \Zenstruck\Foundry\FactoryCollection<\App\Domain\Blog\Entity\Tag|\Zenstruck\Foundry\Persistence\Proxy>                          many(int $min, int|null $max = null)
 * @method        \Zenstruck\Foundry\FactoryCollection<\App\Domain\Blog\Entity\Tag|\Zenstruck\Foundry\Persistence\Proxy>                          sequence(iterable|callable $sequence)
 * @method static \Zenstruck\Foundry\Persistence\ProxyRepositoryDecorator<\App\Domain\Blog\Entity\Tag, \App\Domain\Blog\Repository\TagRepository> repository()
 *
 * @phpstan-method \App\Domain\Blog\Entity\Tag&\Zenstruck\Foundry\Persistence\Proxy<\App\Domain\Blog\Entity\Tag> create(array|callable $attributes = [])
 * @phpstan-method static \App\Domain\Blog\Entity\Tag&\Zenstruck\Foundry\Persistence\Proxy<\App\Domain\Blog\Entity\Tag> createOne(array $attributes = [])
 * @phpstan-method static \App\Domain\Blog\Entity\Tag&\Zenstruck\Foundry\Persistence\Proxy<\App\Domain\Blog\Entity\Tag> find(object|array|mixed $criteria)
 * @phpstan-method static \App\Domain\Blog\Entity\Tag&\Zenstruck\Foundry\Persistence\Proxy<\App\Domain\Blog\Entity\Tag> findOrCreate(array $attributes)
 * @phpstan-method static \App\Domain\Blog\Entity\Tag&\Zenstruck\Foundry\Persistence\Proxy<\App\Domain\Blog\Entity\Tag> first(string $sortedField = 'id')
 * @phpstan-method static \App\Domain\Blog\Entity\Tag&\Zenstruck\Foundry\Persistence\Proxy<\App\Domain\Blog\Entity\Tag> last(string $sortedField = 'id')
 * @phpstan-method static \App\Domain\Blog\Entity\Tag&\Zenstruck\Foundry\Persistence\Proxy<\App\Domain\Blog\Entity\Tag> random(array $attributes = [])
 * @phpstan-method static \App\Domain\Blog\Entity\Tag&\Zenstruck\Foundry\Persistence\Proxy<\App\Domain\Blog\Entity\Tag> randomOrCreate(array $attributes = [])
 * @phpstan-method static list<\App\Domain\Blog\Entity\Tag&\Zenstruck\Foundry\Persistence\Proxy<\App\Domain\Blog\Entity\Tag>> all()
 * @phpstan-method static list<\App\Domain\Blog\Entity\Tag&\Zenstruck\Foundry\Persistence\Proxy<\App\Domain\Blog\Entity\Tag>> createMany(int $number, array|callable $attributes = [])
 * @phpstan-method static list<\App\Domain\Blog\Entity\Tag&\Zenstruck\Foundry\Persistence\Proxy<\App\Domain\Blog\Entity\Tag>> createSequence(iterable|callable $sequence)
 * @phpstan-method static list<\App\Domain\Blog\Entity\Tag&\Zenstruck\Foundry\Persistence\Proxy<\App\Domain\Blog\Entity\Tag>> findBy(array $attributes)
 * @phpstan-method static list<\App\Domain\Blog\Entity\Tag&\Zenstruck\Foundry\Persistence\Proxy<\App\Domain\Blog\Entity\Tag>> randomRange(int $min, int $max, array $attributes = [])
 * @phpstan-method static list<\App\Domain\Blog\Entity\Tag&\Zenstruck\Foundry\Persistence\Proxy<\App\Domain\Blog\Entity\Tag>> randomSet(int $number, array $attributes = [])
 * @phpstan-method \Zenstruck\Foundry\FactoryCollection<\App\Domain\Blog\Entity\Tag&\Zenstruck\Foundry\Persistence\Proxy<\App\Domain\Blog\Entity\Tag>> many(int $min, int|null $max = null)
 * @phpstan-method \Zenstruck\Foundry\FactoryCollection<\App\Domain\Blog\Entity\Tag&\Zenstruck\Foundry\Persistence\Proxy<\App\Domain\Blog\Entity\Tag>> sequence(iterable|callable $sequence)
 *
 * @extends \Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory<\App\Domain\Blog\Entity\Tag>
 */
final class TagFactory extends \Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory
{
    public function withName(string $name): self
    {
        return $this->with(['name' => $name]);
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaults(): array
    {
        return [
            'name' => self::faker()->unique()->word(),
        ];
    }

    protected function initialize(): static
    {
        return $this
             // ->afterInstantiate(function (Tag $post): void {})
        ;
    }

    public static function class(): string
    {
        return Tag::class;
    }
}
