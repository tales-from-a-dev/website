<?php

declare(strict_types=1);

namespace App\Infrastructure\State\Processor;

use App\Domain\Entity\Settings;
use App\Domain\State\ProcessorInterface;
use App\Ui\Form\Data\SettingsDto;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;
use Webmozart\Assert\Assert;

/**
 * @implements ProcessorInterface<SettingsDto|null, Settings>
 */
final readonly class UpdateSettingsProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ObjectMapperInterface $objectMapper,
    ) {
    }

    /**
     * @param array{previous_data?: Settings|null} $context
     */
    public function process(mixed $data, array $context = []): Settings
    {
        Assert::isInstanceOf($data, SettingsDto::class);
        Assert::notNull($data->available);
        Assert::notNull($data->availableAt);
        Assert::notNull($data->averageDailyRate);

        $previousData = $context['previous_data'] ?? null;
        Assert::isInstanceOf($previousData, Settings::class);
        Assert::notNull($previousData->id);

        $this->objectMapper->map($data, $previousData);
        $this->entityManager->flush();

        return $previousData;
    }
}
