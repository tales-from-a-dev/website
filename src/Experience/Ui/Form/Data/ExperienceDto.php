<?php

declare(strict_types=1);

namespace App\Experience\Ui\Form\Data;

use App\Experience\Domain\Enum\ExperiencePositionEnum;
use App\Experience\Domain\Enum\ExperienceTypeEnum;
use Symfony\Component\Validator\Constraints as Assert;

final class ExperienceDto
{
    /**
     * @param string[] $technologies
     */
    public function __construct(
        #[Assert\Type(type: 'string')]
        #[Assert\NotBlank]
        #[Assert\Length(min: 1, max: 255)]
        public ?string $company = null,

        #[Assert\Type(type: ExperienceTypeEnum::class)]
        public ?ExperienceTypeEnum $type = null,

        #[Assert\Type(type: ExperiencePositionEnum::class)]
        public ?ExperiencePositionEnum $position = null,

        #[Assert\Type(type: 'string')]
        public ?string $description = null,

        #[Assert\All([
            new Assert\NotBlank(),
            new Assert\Length(min: 2),
        ])]
        public array $technologies = [],

        #[Assert\NotBlank]
        #[Assert\Type(type: \DateTimeImmutable::class)]
        public ?\DateTimeImmutable $startAt = null,

        #[Assert\NotBlank(allowNull: true)]
        #[Assert\GreaterThan(propertyPath: 'startAt')]
        public ?\DateTimeImmutable $endAt = null,
    ) {
    }
}
