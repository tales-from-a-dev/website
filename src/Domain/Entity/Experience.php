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
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private string $company;

    #[ORM\Column(type: Types::STRING, length: 255, enumType: ExperienceTypeEnum::class)]
    private ExperienceTypeEnum $type;

    #[ORM\Column(type: Types::STRING, length: 255, enumType: ExperiencePositionEnum::class)]
    private ExperiencePositionEnum $position;

    #[ORM\Column(type: Types::TEXT, length: 255, options: ['default' => ''])]
    private string $description = '';

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private \DateTimeImmutable $startAt;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $endAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCompany(): string
    {
        return $this->company;
    }

    public function setCompany(string $company): void
    {
        $this->company = $company;
    }

    public function getType(): ExperienceTypeEnum
    {
        return $this->type;
    }

    public function setType(ExperienceTypeEnum $type): void
    {
        $this->type = $type;
    }

    public function getPosition(): ExperiencePositionEnum
    {
        return $this->position;
    }

    public function setPosition(ExperiencePositionEnum $position): void
    {
        $this->position = $position;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getStartAt(): \DateTimeImmutable
    {
        return $this->startAt;
    }

    public function setStartAt(\DateTimeImmutable $startAt): void
    {
        $this->startAt = $startAt;
    }

    public function getEndAt(): ?\DateTimeImmutable
    {
        return $this->endAt;
    }

    public function setEndAt(?\DateTimeImmutable $endAt): void
    {
        $this->endAt = $endAt;
    }
}
