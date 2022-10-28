<?php

declare(strict_types=1);

namespace App\Domain\Project\Factory;

use App\Domain\Project\Entity\Project;
use App\Domain\Project\Enum\ProjectType;
use App\Domain\Project\Model\GitHubProject;
use App\Domain\Project\Repository\ProjectRepository;
use Elao\Enum\Bridge\Faker\Provider\EnumProvider;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Project>
 *
 * @method static Project|Proxy                     createOne(array $attributes = [])
 * @method static Project[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static Project[]|Proxy[]                 createSequence(array|callable $sequence)
 * @method static Project|Proxy                     find(object|array|mixed $criteria)
 * @method static Project|Proxy                     findOrCreate(array $attributes)
 * @method static Project|Proxy                     first(string $sortedField = 'id')
 * @method static Project|Proxy                     last(string $sortedField = 'id')
 * @method static Project|Proxy                     random(array $attributes = [])
 * @method static Project|Proxy                     randomOrCreate(array $attributes = [])
 * @method static Project[]|Proxy[]                 all()
 * @method static Project[]|Proxy[]                 findBy(array $attributes)
 * @method static Project[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 * @method static Project[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static ProjectRepository|RepositoryProxy repository()
 * @method        Project|Proxy                     create(array|callable $attributes = [])
 */
final class ProjectFactory extends ModelFactory
{
    public function __construct()
    {
        parent::__construct();

        self::faker()->addProvider(new EnumProvider());
    }

    public function withType(ProjectType $type): self
    {
        return $this->addState(['type' => $type]);
    }

    public function withGitHubProject(): self
    {
        return $this->addState(['metadata' => new GitHubProject(
            self::faker()->uuid(),
            self::faker()->numberBetween(0, 10),
            self::faker()->numberBetween(0, 10),
            self::faker()->randomElements(
                ['php', 'javascript', 'css', 'html', 'twig'],
                self::faker()->numberBetween(0, 3)
            ),
        )]);
    }

    /**
     * @return array<string, mixed>
     */
    protected function getDefaults(): array
    {
        return [
            // TODO add your default values here (https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories)
            'title' => self::faker()->text(),
            'subTitle' => self::faker()->text(),
            'description' => self::faker()->text(),
            'type' => self::faker()->randomEnum(ProjectType::class),
            'url' => self::faker()->text(),
            'slug' => self::faker()->text(),
            'createdAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
            'updatedAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
        ];
    }

    protected function initialize(): self
    {
        // see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
        return $this
            // ->afterInstantiate(function(Project $project): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Project::class;
    }
}
