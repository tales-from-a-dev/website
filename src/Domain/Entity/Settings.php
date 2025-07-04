<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Infrastructure\Repository\SettingsRepository;
use App\Ui\Form\Data\SettingsDto;
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
    public ?int $id = null;

    #[ORM\Column(type: Types::SMALLINT, options: ['default' => 500])]
    public int $averageDailyRate;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => false])]
    public bool $available;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    public ?\DateTimeImmutable $availableAt = null;
}
