<?php

declare(strict_types=1);

namespace App\Domain\Dto;

use App\Domain\Entity\Settings;
use Symfony\Component\ObjectMapper\Attribute\Map;
use Symfony\Component\Validator\Constraints as Assert;

#[Map(target: Settings::class)]
final class SettingsDto
{
    public function __construct(
        #[Assert\NotNull]
        public ?bool $available = null,

        #[Assert\GreaterThanOrEqual('today')]
        public ?\DateTimeInterface $availableAt = null,

        #[Assert\NotBlank]
        #[Assert\Positive]
        #[Assert\Range(min: 1, max: 1000)]
        public ?int $averageDailyRate = null,
    ) {
    }
}
