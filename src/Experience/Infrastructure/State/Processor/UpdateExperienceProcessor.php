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
final readonly class UpdateExperienceProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ObjectMapperInterface $objectMapper,
    ) {
    }

    /**
     * @param array{previous_data?: Experience|null} $context
     */
    public function process(mixed $data, array $context = []): Experience
    {
        Assert::isInstanceOf($data, ExperienceDto::class);
        Assert::notNull($data->company);
        Assert::notNull($data->type);
        Assert::notNull($data->position);
        Assert::notNull($data->startAt);

        $previousData = $context['previous_data'] ?? null;
        Assert::isInstanceOf($previousData, Experience::class);
        Assert::notNull($previousData->id);

        $this->objectMapper->map($data, $previousData);
        $this->entityManager->flush();

        return $previousData;
    }
}
