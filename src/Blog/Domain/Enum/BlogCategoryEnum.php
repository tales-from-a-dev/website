<?php

declare(strict_types=1);

namespace App\Blog\Domain\Enum;

use Elao\Enum\Attribute\ReadableEnum;
use Elao\Enum\Bridge\Symfony\Translation\TranslatableEnumInterface;
use Elao\Enum\Bridge\Symfony\Translation\TranslatableEnumTrait;

#[ReadableEnum(prefix: 'enum.blog_category.', useValueAsDefault: true)]
enum BlogCategoryEnum: string implements TranslatableEnumInterface
{
    use TranslatableEnumTrait;

    case Ai = 'ai';
    case Architecture = 'architecture';
    case Career = 'career';
    case DevOps = 'dev-ops';
    case Notes = 'notes';
    case Performance = 'performance';
    case Security = 'security';
    case Testing = 'testing';
}
