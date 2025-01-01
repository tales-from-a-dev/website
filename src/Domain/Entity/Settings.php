<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\Dto\SettingsDto;
use App\Infrastructure\Repository\SettingsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\ObjectMapper\Attribute\Map;

#[ORM\Entity(repositoryClass: SettingsRepository::class)]
#[Map(source: SettingsDto::class)]
class Settings
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::SMALLINT, options: ['default' => 500])]
    private int $averageDailyRate;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => false])]
    private bool $available;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $availableAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAverageDailyRate(): int
    {
        return $this->averageDailyRate;
    }

    public function setAverageDailyRate(int $averageDailyRate): void
    {
        $this->averageDailyRate = $averageDailyRate;
    }

    public function isAvailable(): bool
    {
        return $this->available;
    }

    public function setAvailable(bool $available): void
    {
        $this->available = $available;
    }

    public function getAvailableAt(): ?\DateTimeImmutable
    {
        return $this->availableAt;
    }

    public function setAvailableAt(?\DateTimeImmutable $availableAt): void
    {
        $this->availableAt = $availableAt;
    }
}
