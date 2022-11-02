<?php

declare(strict_types=1);

namespace App\Domain\Blog\Factory;

use App\Domain\Blog\Entity\Post;
use App\Domain\Blog\Enum\PublicationStatus;
use App\Domain\Blog\Repository\PostRepository;
use Elao\Enum\Bridge\Faker\Provider\EnumProvider;
use Monolog\DateTimeImmutable;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Post>
 *
 * @method static Post|Proxy                     createOne(array $attributes = [])
 * @method static Post[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static Post[]|Proxy[]                 createSequence(array|callable $sequence)
 * @method static Post|Proxy                     find(object|array|mixed $criteria)
 * @method static Post|Proxy                     findOrCreate(array $attributes)
 * @method static Post|Proxy                     first(string $sortedField = 'id')
 * @method static Post|Proxy                     last(string $sortedField = 'id')
 * @method static Post|Proxy                     random(array $attributes = [])
 * @method static Post|Proxy                     randomOrCreate(array $attributes = [])
 * @method static Post[]|Proxy[]                 all()
 * @method static Post[]|Proxy[]                 findBy(array $attributes)
 * @method static Post[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 * @method static Post[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static PostRepository|RepositoryProxy repository()
 * @method        Post|Proxy                     create(array|callable $attributes = [])
 */
final class PostFactory extends ModelFactory
{
    public function __construct()
    {
        parent::__construct();

        self::faker()->addProvider(new EnumProvider());
    }

    public function draft(): self
    {
        return $this->addState(['publicationStatus' => PublicationStatus::Draft]);
    }

    public function frozen(): self
    {
        return $this->addState(['publicationStatus' => PublicationStatus::Frozen]);
    }

    public function published(): self
    {
        return $this->addState(['publicationStatus' => PublicationStatus::Published]);
    }

    public function publishedInFuture(): self
    {
        return $this->addState([
            'publicationStatus' => PublicationStatus::Published,
            'publishedAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTimeBetween('tomorrow', '+6 months')),
        ]);
    }

    public function withTitle(string $title): self
    {
        return $this->addState(['title' => $title]);
    }

    /**
     * @return array<string, mixed>
     */
    protected function getDefaults(): array
    {
        return [
            // TODO add your default values here (https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories)
            'title' => self::faker()->text(50),
            'content' => self::faker()->text(1000),
            'publicationStatus' => self::faker()->randomEnum(PublicationStatus::class),
        ];
    }

    protected function initialize(): self
    {
        // see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
        return $this
             ->afterInstantiate(function (Post $post): void {
                 if (PublicationStatus::Published === $post->getPublicationStatus() && null === $post->getPublishedAt()) {
                     $post->setPublishedAt(DateTimeImmutable::createFromMutable(self::faker()->dateTimeBetween('-6 months')));
                 }
             })
        ;
    }

    protected static function getClass(): string
    {
        return Post::class;
    }
}
