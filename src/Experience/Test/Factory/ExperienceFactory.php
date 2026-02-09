<?php

declare(strict_types=1);

namespace App\Experience\Test\Factory;

use App\Experience\Domain\Entity\Experience;
use App\Experience\Domain\Enum\ExperiencePositionEnum;
use App\Experience\Domain\Enum\ExperienceTypeEnum;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<Experience>
 */
final class ExperienceFactory extends PersistentObjectFactory
{
    #[\Override]
    public static function class(): string
    {
        return Experience::class;
    }

    #[\Override]
    protected function defaults(): array
    {
        return [
            'company' => self::faker()->company(),
            'type' => self::faker()->randomElement(ExperienceTypeEnum::cases()),
            'position' => self::faker()->randomElement(ExperiencePositionEnum::cases()),
            'description' => self::faker()->text(255),
            'technologies' => self::faker()->randomElements([
                'PHP', 'Javascript', 'Typescript', 'Go', 'CSS', 'HTML',
                'Symfony', 'Laravel', 'CakePHP',
                'React', 'Vue', 'Angular', 'Svelte', 'Stimulus', 'Tailwind CSS',
                'Docker', 'AWS', 'Azure',
            ]),
            'startAt' => $startAt = \DateTimeImmutable::createFromMutable(self::faker()->dateTimeThisDecade()),
            'endAt' => $startAt->modify(\sprintf('+%d years', self::faker()->numberBetween(1, 5))),
        ];
    }
}
