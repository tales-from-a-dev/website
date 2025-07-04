<?php

declare(strict_types=1);

namespace App\Ui\Command\Database;

use App\Domain\Entity\Experience;
use App\Domain\Entity\Settings;
use App\Domain\Enum\ExperiencePositionEnum;
use App\Domain\Enum\ExperienceTypeEnum;
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

        foreach ($this->getExperienceData() as [$company, $type, $position, $startAt, $endAt]) {
            if (!$this->entityManager->getRepository(Experience::class)->findOneBy(['company' => $company])) {
                $experience = new Experience();
                $experience->company = $company;
                $experience->type = $type;
                $experience->position = $position;
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
            new \DateTimeImmutable('2010-11-01'),
            new \DateTimeImmutable('2011-06-01'),
        ];
        yield [
            'Keops Infocentre',
            ExperienceTypeEnum::PermanentContract,
            ExperiencePositionEnum::FullstackDeveloper,
            new \DateTimeImmutable('2012-06-01'),
            new \DateTimeImmutable('2015-03-01'),
        ];
        yield [
            'Emakina',
            ExperienceTypeEnum::PermanentContract,
            ExperiencePositionEnum::BackendDeveloper,
            new \DateTimeImmutable('2015-11-01'),
            new \DateTimeImmutable('2020-10-01'),
        ];
        yield [
            'Pixine',
            ExperienceTypeEnum::PermanentContract,
            ExperiencePositionEnum::BackendDeveloper,
            new \DateTimeImmutable('2020-12-01'),
            new \DateTimeImmutable('2022-10-01'),
        ];
        yield [
            'Obat',
            ExperienceTypeEnum::Freelance,
            ExperiencePositionEnum::BackendDeveloper,
            new \DateTimeImmutable('2024-05-01'),
            new \DateTimeImmutable('2024-10-01'),
        ];
        yield [
            'DotWorld',
            ExperienceTypeEnum::Freelance,
            ExperiencePositionEnum::FullstackDeveloper,
            new \DateTimeImmutable('2024-12-01'),
            new \DateTimeImmutable('2025-06-01'),
        ];
    }
}
