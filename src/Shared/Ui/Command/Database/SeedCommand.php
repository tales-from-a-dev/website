<?php

declare(strict_types=1);

namespace App\Shared\Ui\Command\Database;

use App\Experience\Domain\Entity\Experience;
use App\Experience\Domain\Enum\ExperiencePositionEnum;
use App\Experience\Domain\Enum\ExperienceTypeEnum;
use App\Settings\Domain\Entity\Settings;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:database:seed',
    description: 'Seed the database'
)]
final readonly class SeedCommand
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function __invoke(SymfonyStyle $io): int
    {
        $io->text('Seeding database...');
        $io->newLine();

        $this->entityManager->beginTransaction();

        $error = false;
        try {
            $this->seedSettings($io);
            $this->seedExperience($io);

            $this->entityManager->flush();
            $this->entityManager->commit();

            $io->success('Database seeded successfully!');
        } catch (\Throwable $exception) {
            $this->entityManager->rollback();

            $io->error([
                'Could not seed the database',
                $exception->getMessage(),
            ]);

            $error = true;
        } finally {
            $this->entityManager->close();

            return $error ? Command::FAILURE : Command::SUCCESS;
        }
    }

    private function seedSettings(SymfonyStyle $io): void
    {
        $io->text(
            \sprintf(
                '    Seed table <info>"%s"</info>',
                $this->entityManager->getClassMetadata(Settings::class)->getTableName(),
            )
        );

        if ([] === $this->entityManager->getRepository(Settings::class)->findAll()) {
            $settings = new Settings();
            $settings->available = true;
            $settings->averageDailyRate = 500;

            $this->entityManager->persist($settings);
        }
    }

    private function seedExperience(SymfonyStyle $io): void
    {
        $io->text(
            \sprintf(
                '    Seed table <info>"%s"</info>',
                $this->entityManager->getClassMetadata(Experience::class)->getTableName(),
            )
        );

        foreach ($this->getExperienceData() as [$company, $type, $position, $description, $technologies, $startAt, $endAt]) {
            if (!$this->entityManager->getRepository(Experience::class)->findOneBy(['company' => $company])) {
                $experience = new Experience();
                $experience->company = $company;
                $experience->type = $type;
                $experience->position = $position;
                $experience->description = $description;
                $experience->technologies = $technologies;
                $experience->startAt = $startAt;
                $experience->endAt = $endAt;

                $this->entityManager->persist($experience);
            }
        }
    }

    /**
     * @return iterable<array<int, mixed>>
     */
    private function getExperienceData(): iterable
    {
        yield [
            'KaGames',
            ExperienceTypeEnum::PermanentContract,
            ExperiencePositionEnum::FullstackDeveloper,
            '',
            ['PHP', 'Zend Framework', 'jQuery', 'MySQL'],
            new \DateTimeImmutable('2010-11-01'),
            new \DateTimeImmutable('2011-06-01'),
        ];
        yield [
            'Keops Infocentre',
            ExperienceTypeEnum::PermanentContract,
            ExperiencePositionEnum::FullstackDeveloper,
            '',
            ['PHP', 'CakePHP', 'jQuery', 'MySQL'],
            new \DateTimeImmutable('2012-06-01'),
            new \DateTimeImmutable('2015-03-01'),
        ];
        yield [
            'Emakina',
            ExperienceTypeEnum::PermanentContract,
            ExperiencePositionEnum::BackendDeveloper,
            '',
            ['Proximis', 'Symfony', 'Zend Framework', 'MySQL', 'Docker', 'AWS'],
            new \DateTimeImmutable('2015-11-01'),
            new \DateTimeImmutable('2020-10-01'),
        ];
        yield [
            'Pixine',
            ExperienceTypeEnum::PermanentContract,
            ExperiencePositionEnum::BackendDeveloper,
            '',
            ['PHP', 'Symfony', 'API Platform', 'Laravel', 'Docker', 'AWS'],
            new \DateTimeImmutable('2020-12-01'),
            new \DateTimeImmutable('2022-10-01'),
        ];
        yield [
            'Obat',
            ExperienceTypeEnum::Freelance,
            ExperiencePositionEnum::BackendDeveloper,
            "Mission de 4 mois afin d'aider à maintenir un legacy en Symfony 5.4 / API Platform 2.6 et à développer de nouvelles fonctionnalités sur une architecture macro service en suivant les modèles de conception DDD / CQRS.",
            ['PHP', 'Symfony', 'API Platform', 'DDD', 'CQRS'],
            new \DateTimeImmutable('2024-05-01'),
            new \DateTimeImmutable('2024-10-01'),
        ];
        yield [
            'DotWorld',
            ExperienceTypeEnum::Freelance,
            ExperiencePositionEnum::FullstackDeveloper,
            "Durant une mission de 6 mois, j'ai contribué au sein de plusieurs squads de DotWorld, à mettre en conformité RGPD plusieurs sites ainsi qu'à faire des montées de versions vers Laravel 11 et Livewire 3",
            ['PHP', 'Laravel', 'Livewire', 'Tailwind CSS'],
            new \DateTimeImmutable('2024-12-01'),
            new \DateTimeImmutable('2025-06-01'),
        ];
    }
}
