<?php

declare(strict_types=1);

namespace App\Experience\Domain\Enum;

use Elao\Enum\Attribute\ReadableEnum;
use Elao\Enum\Bridge\Symfony\Translation\TranslatableEnumInterface;
use Elao\Enum\Bridge\Symfony\Translation\TranslatableEnumTrait;

#[ReadableEnum(prefix: 'enum.experience_position.', useValueAsDefault: true)]
enum ExperiencePositionEnum: string implements TranslatableEnumInterface
{
    use TranslatableEnumTrait;

    case BackendDeveloper = 'backend_developer';
    case FrontendDeveloper = 'frontend_developer';
    case FullstackDeveloper = 'fullstack_developer';
}
