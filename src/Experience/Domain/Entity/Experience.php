<?php

declare(strict_types=1);

namespace App\Experience\Domain\Entity;

use App\Experience\Domain\Enum\ExperiencePositionEnum;
use App\Experience\Domain\Enum\ExperienceTypeEnum;
use App\Experience\Infrastructure\Repository\ExperienceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as Orm;

#[Orm\Entity(repositoryClass: ExperienceRepository::class)]
class Experience
{
    #[Orm\Id]
    #[Orm\GeneratedValue]
    #[Orm\Column]
    public ?int $id = null;

    #[Orm\Column(type: Types::STRING, length: 255)]
    public string $company;

    #[Orm\Column(type: Types::STRING, length: 255, enumType: ExperienceTypeEnum::class)]
    public ExperienceTypeEnum $type;

    #[Orm\Column(type: Types::STRING, length: 255, enumType: ExperiencePositionEnum::class)]
    public ExperiencePositionEnum $position;

    #[Orm\Column(type: Types::TEXT, length: 255, options: ['default' => ''])]
    public string $description = '';

    /**
     * @var string[]
     */
    #[Orm\Column(type: Types::JSONB, options: ['jsonb' => true, 'default' => '[]'])]
    public array $technologies = [];

    #[Orm\Column(type: Types::DATE_IMMUTABLE)]
    public \DateTimeImmutable $startAt;

    #[Orm\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    public ?\DateTimeImmutable $endAt = null;
}
