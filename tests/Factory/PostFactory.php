<?php

declare(strict_types=1);

namespace App\Tests\Factory;

use App\Domain\Blog\Entity\Post;
use App\Domain\Blog\Enum\PublicationStatus;
use App\Tests\Faker\MarkdownProvider;

/**
 * @method        \App\Domain\Blog\Entity\Post|\Zenstruck\Foundry\Persistence\Proxy                                                                 create(array|callable $attributes = [])
 * @method static \App\Domain\Blog\Entity\Post|\Zenstruck\Foundry\Persistence\Proxy                                                                 createOne(array $attributes = [])
 * @method static \App\Domain\Blog\Entity\Post|\Zenstruck\Foundry\Persistence\Proxy                                                                 find(object|array|mixed $criteria)
 * @method static \App\Domain\Blog\Entity\Post|\Zenstruck\Foundry\Persistence\Proxy                                                                 findOrCreate(array $attributes)
 * @method static \App\Domain\Blog\Entity\Post|\Zenstruck\Foundry\Persistence\Proxy                                                                 first(string $sortedField = 'id')
 * @method static \App\Domain\Blog\Entity\Post|\Zenstruck\Foundry\Persistence\Proxy                                                                 last(string $sortedField = 'id')
 * @method static \App\Domain\Blog\Entity\Post|\Zenstruck\Foundry\Persistence\Proxy                                                                 random(array $attributes = [])
 * @method static \App\Domain\Blog\Entity\Post|\Zenstruck\Foundry\Persistence\Proxy                                                                 randomOrCreate(array $attributes = [])
 * @method static \App\Domain\Blog\Entity\Post[]|\Zenstruck\Foundry\Persistence\Proxy[]                                                             all()
 * @method static \App\Domain\Blog\Entity\Post[]|\Zenstruck\Foundry\Persistence\Proxy[]                                                             createMany(int $number, array|callable $attributes = [])
 * @method static \App\Domain\Blog\Entity\Post[]|\Zenstruck\Foundry\Persistence\Proxy[]                                                             createSequence(iterable|callable $sequence)
 * @method static \App\Domain\Blog\Entity\Post[]|\Zenstruck\Foundry\Persistence\Proxy[]                                                             findBy(array $attributes)
 * @method static \App\Domain\Blog\Entity\Post[]|\Zenstruck\Foundry\Persistence\Proxy[]                                                             randomRange(int $min, int $max, array $attributes = [])
 * @method static \App\Domain\Blog\Entity\Post[]|\Zenstruck\Foundry\Persistence\Proxy[]                                                             randomSet(int $number, array $attributes = [])
 * @method        \Zenstruck\Foundry\FactoryCollection<\App\Domain\Blog\Entity\Post|\Zenstruck\Foundry\Persistence\Proxy>                           many(int $min, int|null $max = null)
 * @method        \Zenstruck\Foundry\FactoryCollection<\App\Domain\Blog\Entity\Post|\Zenstruck\Foundry\Persistence\Proxy>                           sequence(iterable|callable $sequence)
 * @method static \Zenstruck\Foundry\Persistence\ProxyRepositoryDecorator<\App\Domain\Blog\Entity\Post, \App\Domain\Blog\Repository\PostRepository> repository()
 *
 * @phpstan-method \App\Domain\Blog\Entity\Post&\Zenstruck\Foundry\Persistence\Proxy<\App\Domain\Blog\Entity\Post> create(array|callable $attributes = [])
 * @phpstan-method static \App\Domain\Blog\Entity\Post&\Zenstruck\Foundry\Persistence\Proxy<\App\Domain\Blog\Entity\Post> createOne(array $attributes = [])
 * @phpstan-method static \App\Domain\Blog\Entity\Post&\Zenstruck\Foundry\Persistence\Proxy<\App\Domain\Blog\Entity\Post> find(object|array|mixed $criteria)
 * @phpstan-method static \App\Domain\Blog\Entity\Post&\Zenstruck\Foundry\Persistence\Proxy<\App\Domain\Blog\Entity\Post> findOrCreate(array $attributes)
 * @phpstan-method static \App\Domain\Blog\Entity\Post&\Zenstruck\Foundry\Persistence\Proxy<\App\Domain\Blog\Entity\Post> first(string $sortedField = 'id')
 * @phpstan-method static \App\Domain\Blog\Entity\Post&\Zenstruck\Foundry\Persistence\Proxy<\App\Domain\Blog\Entity\Post> last(string $sortedField = 'id')
 * @phpstan-method static \App\Domain\Blog\Entity\Post&\Zenstruck\Foundry\Persistence\Proxy<\App\Domain\Blog\Entity\Post> random(array $attributes = [])
 * @phpstan-method static \App\Domain\Blog\Entity\Post&\Zenstruck\Foundry\Persistence\Proxy<\App\Domain\Blog\Entity\Post> randomOrCreate(array $attributes = [])
 * @phpstan-method static list<\App\Domain\Blog\Entity\Post&\Zenstruck\Foundry\Persistence\Proxy<\App\Domain\Blog\Entity\Post>> all()
 * @phpstan-method static list<\App\Domain\Blog\Entity\Post&\Zenstruck\Foundry\Persistence\Proxy<\App\Domain\Blog\Entity\Post>> createMany(int $number, array|callable $attributes = [])
 * @phpstan-method static list<\App\Domain\Blog\Entity\Post&\Zenstruck\Foundry\Persistence\Proxy<\App\Domain\Blog\Entity\Post>> createSequence(iterable|callable $sequence)
 * @phpstan-method static list<\App\Domain\Blog\Entity\Post&\Zenstruck\Foundry\Persistence\Proxy<\App\Domain\Blog\Entity\Post>> findBy(array $attributes)
 * @phpstan-method static list<\App\Domain\Blog\Entity\Post&\Zenstruck\Foundry\Persistence\Proxy<\App\Domain\Blog\Entity\Post>> randomRange(int $min, int $max, array $attributes = [])
 * @phpstan-method static list<\App\Domain\Blog\Entity\Post&\Zenstruck\Foundry\Persistence\Proxy<\App\Domain\Blog\Entity\Post>> randomSet(int $number, array $attributes = [])
 * @phpstan-method \Zenstruck\Foundry\FactoryCollection<\App\Domain\Blog\Entity\Post&\Zenstruck\Foundry\Persistence\Proxy<\App\Domain\Blog\Entity\Post>> many(int $min, int|null $max = null)
 * @phpstan-method \Zenstruck\Foundry\FactoryCollection<\App\Domain\Blog\Entity\Post&\Zenstruck\Foundry\Persistence\Proxy<\App\Domain\Blog\Entity\Post>> sequence(iterable|callable $sequence)
 *
 * @extends \Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory<\App\Domain\Blog\Entity\Post>
 */
final class PostFactory extends \Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory
{
    public function __construct()
    {
        parent::__construct();

        self::faker()->addProvider(new MarkdownProvider(self::faker()));
    }

    public function draft(): self
    {
        return $this->with(['publicationStatus' => PublicationStatus::Draft]);
    }

    public function frozen(): self
    {
        return $this->with(['publicationStatus' => PublicationStatus::Frozen]);
    }

    public function published(): self
    {
        return $this->with(['publicationStatus' => PublicationStatus::Published]);
    }

    public function publishedInFuture(): self
    {
        return $this->with(fn () => [
            'publicationStatus' => PublicationStatus::Published,
            'publishedAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTimeBetween('tomorrow', '+6 months')),
        ]);
    }

    public function withPublicationStatus(PublicationStatus $status): self
    {
        return $this->with(['publicationStatus' => $status]);
    }

    public function withTitle(string $title): self
    {
        return $this->with(['title' => $title]);
    }

    public function withTags(int $min, int $max): self
    {
        return $this->with(fn () => ['tags' => TagFactory::randomRange($min, $max)]);
    }

    public function withSpecificTag(mixed $tag): self
    {
        return $this->with(['tags' => [$tag]]);
    }

    /**
     * @return array<string, mixed>
     */
    #[\Override]
    protected function defaults(): array
    {
        return [
            'title' => self::faker()->text(50),
            'content' => self::faker()->markdown(),
            'publicationStatus' => self::faker()->randomElement(PublicationStatus::class),
        ];
    }

    #[\Override]
    protected function initialize(): static
    {
        return $this
             ->afterInstantiate(function (Post $post): void {
                 if (PublicationStatus::Published === $post->getPublicationStatus() && null === $post->getPublishedAt()) {
                     $post->setPublishedAt(\DateTimeImmutable::createFromMutable(self::faker()->dateTimeBetween('-6 months')));
                 }
             })
        ;
    }

    #[\Override]
    public static function class(): string
    {
        return Post::class;
    }
}
