<?php

declare(strict_types=1);

namespace App\Tests\Factory;

use App\Domain\Project\Entity\Project;
use App\Domain\Project\Enum\ProjectType;
use App\Domain\Project\Model\GitHubProject;
use Elao\Enum\Bridge\Faker\Provider\EnumProvider;

/**
 * @method        \App\Domain\Project\Entity\Project|\Zenstruck\Foundry\Persistence\Proxy                                                                       create(array|callable $attributes = [])
 * @method static \App\Domain\Project\Entity\Project|\Zenstruck\Foundry\Persistence\Proxy                                                                       createOne(array $attributes = [])
 * @method static \App\Domain\Project\Entity\Project|\Zenstruck\Foundry\Persistence\Proxy                                                                       find(object|array|mixed $criteria)
 * @method static \App\Domain\Project\Entity\Project|\Zenstruck\Foundry\Persistence\Proxy                                                                       findOrCreate(array $attributes)
 * @method static \App\Domain\Project\Entity\Project|\Zenstruck\Foundry\Persistence\Proxy                                                                       first(string $sortedField = 'id')
 * @method static \App\Domain\Project\Entity\Project|\Zenstruck\Foundry\Persistence\Proxy                                                                       last(string $sortedField = 'id')
 * @method static \App\Domain\Project\Entity\Project|\Zenstruck\Foundry\Persistence\Proxy                                                                       random(array $attributes = [])
 * @method static \App\Domain\Project\Entity\Project|\Zenstruck\Foundry\Persistence\Proxy                                                                       randomOrCreate(array $attributes = [])
 * @method static \App\Domain\Project\Entity\Project[]|\Zenstruck\Foundry\Persistence\Proxy[]                                                                   all()
 * @method static \App\Domain\Project\Entity\Project[]|\Zenstruck\Foundry\Persistence\Proxy[]                                                                   createMany(int $number, array|callable $attributes = [])
 * @method static \App\Domain\Project\Entity\Project[]|\Zenstruck\Foundry\Persistence\Proxy[]                                                                   createSequence(iterable|callable $sequence)
 * @method static \App\Domain\Project\Entity\Project[]|\Zenstruck\Foundry\Persistence\Proxy[]                                                                   findBy(array $attributes)
 * @method static \App\Domain\Project\Entity\Project[]|\Zenstruck\Foundry\Persistence\Proxy[]                                                                   randomRange(int $min, int $max, array $attributes = [])
 * @method static \App\Domain\Project\Entity\Project[]|\Zenstruck\Foundry\Persistence\Proxy[]                                                                   randomSet(int $number, array $attributes = [])
 * @method        \Zenstruck\Foundry\FactoryCollection<\App\Domain\Project\Entity\Project|\Zenstruck\Foundry\Persistence\Proxy>                                 many(int $min, int|null $max = null)
 * @method        \Zenstruck\Foundry\FactoryCollection<\App\Domain\Project\Entity\Project|\Zenstruck\Foundry\Persistence\Proxy>                                 sequence(iterable|callable $sequence)
 * @method static \Zenstruck\Foundry\Persistence\ProxyRepositoryDecorator<\App\Domain\Project\Entity\Project, \App\Domain\Project\Repository\ProjectRepository> repository()
 *
 * @phpstan-method \App\Domain\Project\Entity\Project&\Zenstruck\Foundry\Persistence\Proxy<\App\Domain\Project\Entity\Project> create(array|callable $attributes = [])
 * @phpstan-method static \App\Domain\Project\Entity\Project&\Zenstruck\Foundry\Persistence\Proxy<\App\Domain\Project\Entity\Project> createOne(array $attributes = [])
 * @phpstan-method static \App\Domain\Project\Entity\Project&\Zenstruck\Foundry\Persistence\Proxy<\App\Domain\Project\Entity\Project> find(object|array|mixed $criteria)
 * @phpstan-method static \App\Domain\Project\Entity\Project&\Zenstruck\Foundry\Persistence\Proxy<\App\Domain\Project\Entity\Project> findOrCreate(array $attributes)
 * @phpstan-method static \App\Domain\Project\Entity\Project&\Zenstruck\Foundry\Persistence\Proxy<\App\Domain\Project\Entity\Project> first(string $sortedField = 'id')
 * @phpstan-method static \App\Domain\Project\Entity\Project&\Zenstruck\Foundry\Persistence\Proxy<\App\Domain\Project\Entity\Project> last(string $sortedField = 'id')
 * @phpstan-method static \App\Domain\Project\Entity\Project&\Zenstruck\Foundry\Persistence\Proxy<\App\Domain\Project\Entity\Project> random(array $attributes = [])
 * @phpstan-method static \App\Domain\Project\Entity\Project&\Zenstruck\Foundry\Persistence\Proxy<\App\Domain\Project\Entity\Project> randomOrCreate(array $attributes = [])
 * @phpstan-method static list<\App\Domain\Project\Entity\Project&\Zenstruck\Foundry\Persistence\Proxy<\App\Domain\Project\Entity\Project>> all()
 * @phpstan-method static list<\App\Domain\Project\Entity\Project&\Zenstruck\Foundry\Persistence\Proxy<\App\Domain\Project\Entity\Project>> createMany(int $number, array|callable $attributes = [])
 * @phpstan-method static list<\App\Domain\Project\Entity\Project&\Zenstruck\Foundry\Persistence\Proxy<\App\Domain\Project\Entity\Project>> createSequence(iterable|callable $sequence)
 * @phpstan-method static list<\App\Domain\Project\Entity\Project&\Zenstruck\Foundry\Persistence\Proxy<\App\Domain\Project\Entity\Project>> findBy(array $attributes)
 * @phpstan-method static list<\App\Domain\Project\Entity\Project&\Zenstruck\Foundry\Persistence\Proxy<\App\Domain\Project\Entity\Project>> randomRange(int $min, int $max, array $attributes = [])
 * @phpstan-method static list<\App\Domain\Project\Entity\Project&\Zenstruck\Foundry\Persistence\Proxy<\App\Domain\Project\Entity\Project>> randomSet(int $number, array $attributes = [])
 * @phpstan-method \Zenstruck\Foundry\FactoryCollection<\App\Domain\Project\Entity\Project&\Zenstruck\Foundry\Persistence\Proxy<\App\Domain\Project\Entity\Project>> many(int $min, int|null $max = null)
 * @phpstan-method \Zenstruck\Foundry\FactoryCollection<\App\Domain\Project\Entity\Project&\Zenstruck\Foundry\Persistence\Proxy<\App\Domain\Project\Entity\Project>> sequence(iterable|callable $sequence)
 *
 * @extends \Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory<\App\Domain\Project\Entity\Project>
 */
final class ProjectFactory extends \Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory
{
    public function __construct()
    {
        parent::__construct();

        self::faker()->addProvider(new EnumProvider());
    }

    public function withType(ProjectType $type): self
    {
        return $this->with(['type' => $type]);
    }

    public function asCustomerProject(): self
    {
        return $this->with(['type' => ProjectType::Customer]);
    }

    public function asGitHubProject(): self
    {
        return $this->with([
            'type' => ProjectType::GitHub,
            'metadata' => $this->buildGitHubMetadata(),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaults(): array
    {
        return [
            'title' => self::faker()->text(50),
            'subTitle' => self::faker()->text(30),
            'description' => self::faker()->text(500),
            'type' => self::faker()->randomElement(ProjectType::class),
            'url' => self::faker()->url(),
        ];
    }

    protected function initialize(): static
    {
        // see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
        return $this
            ->afterInstantiate(function (Project $project): void {
                if ($project->isGitHub() && null === $project->getMetadata()) {
                    $project->setMetadata($this->buildGitHubMetadata());
                }
            })
        ;
    }

    public static function class(): string
    {
        return Project::class;
    }

    private function buildGitHubMetadata(): GitHubProject
    {
        return new GitHubProject(
            self::faker()->uuid(),
            self::faker()->numberBetween(0, 10),
            self::faker()->numberBetween(0, 10),
            self::faker()->randomElements(
                ['php', 'javascript', 'css', 'html', 'twig'],
                self::faker()->numberBetween(0, 3)
            ),
        );
    }
}
