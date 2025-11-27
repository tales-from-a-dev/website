<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Infrastructure\Repository\SettingsRepository;
use App\Ui\Form\Data\SettingsDto;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as Orm;
use Symfony\Component\ObjectMapper\Attribute\Map;

#[Orm\Entity(repositoryClass: SettingsRepository::class)]
#[Map(source: SettingsDto::class)]
class Settings
{
    #[Orm\Id]
    #[Orm\GeneratedValue]
    #[Orm\Column]
    public ?int $id = null;

    #[Orm\Column(type: Types::SMALLINT, options: ['default' => 500])]
    public int $averageDailyRate;

    #[Orm\Column(type: Types::BOOLEAN, options: ['default' => false])]
    public bool $available;

    #[Orm\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    public ?\DateTimeImmutable $availableAt = null;
}
