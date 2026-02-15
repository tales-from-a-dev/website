<?php

declare(strict_types=1);

namespace App\Experience\Infrastructure\State\Processor;

use App\Experience\Domain\Entity\Experience;
use App\Experience\Ui\Form\Data\ExperienceDto;
use App\Shared\Domain\State\ProcessorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;
use Webmozart\Assert\Assert;

/**
 * @implements ProcessorInterface<ExperienceDto|null, Experience>
 */
final readonly class CreateExperienceProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ObjectMapperInterface $objectMapper,
    ) {
    }

    public function process(mixed $data, array $context = []): Experience
    {
        Assert::isInstanceOf($data, ExperienceDto::class);
        Assert::notNull($data->company);
        Assert::notNull($data->type);
        Assert::notNull($data->position);
        Assert::notNull($data->startAt);

        $experience = $this->objectMapper->map($data, Experience::class);

        $this->entityManager->persist($experience);
        $this->entityManager->flush();

        return $experience;
    }
}
