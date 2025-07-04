<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\Enum\ExperiencePositionEnum;
use App\Domain\Enum\ExperienceTypeEnum;
use App\Infrastructure\Repository\ExperienceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ExperienceRepository::class)]
class Experience
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 255)]
    public string $company;

    #[ORM\Column(type: Types::STRING, length: 255, enumType: ExperienceTypeEnum::class)]
    public ExperienceTypeEnum $type;

    #[ORM\Column(type: Types::STRING, length: 255, enumType: ExperiencePositionEnum::class)]
    public ExperiencePositionEnum $position;

    #[ORM\Column(type: Types::TEXT, length: 255, options: ['default' => ''])]
    public string $description = '';

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    public \DateTimeImmutable $startAt;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    public ?\DateTimeImmutable $endAt = null;
}
