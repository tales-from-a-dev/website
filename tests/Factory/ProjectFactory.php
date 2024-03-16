<?php

declare(strict_types=1);

namespace App\Tests\Factory;

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

    public function asCustomerProject(): self
    {
        return $this->addState(['type' => ProjectType::Customer]);
    }

    public function asGitHubProject(): self
    {
        return $this->addState([
            'type' => ProjectType::GitHub,
            'metadata' => $this->buildGitHubMetadata(),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    protected function getDefaults(): array
    {
        return [
            'title' => self::faker()->text(50),
            'subTitle' => self::faker()->text(30),
            'description' => self::faker()->text(500),
            'type' => self::faker()->randomElement(ProjectType::class),
            'url' => self::faker()->url(),
        ];
    }

    protected function initialize(): self
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

    protected static function getClass(): string
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
