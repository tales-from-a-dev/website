<?php

declare(strict_types=1);

namespace App\Experience\Domain\Enum;

use Elao\Enum\Attribute\ReadableEnum;
use Elao\Enum\Bridge\Symfony\Translation\TranslatableEnumInterface;
use Elao\Enum\Bridge\Symfony\Translation\TranslatableEnumTrait;

#[ReadableEnum(prefix: 'enum.experience_type.', useValueAsDefault: true)]
enum ExperienceTypeEnum: string implements TranslatableEnumInterface
{
    use TranslatableEnumTrait;

    case Freelance = 'freelance';
    case PermanentContract = 'permanent_contract';

    public function isPermanentContract(): bool
    {
        return self::PermanentContract === $this;
    }

    public function isFreelance(): bool
    {
        return self::Freelance === $this;
    }
}
