<?php

declare(strict_types=1);

namespace App\Blog\Domain\Enum;

use Elao\Enum\Attribute\EnumCase;
use Elao\Enum\Attribute\ReadableEnum;
use Elao\Enum\Bridge\Symfony\Translation\TranslatableEnumInterface;
use Elao\Enum\Bridge\Symfony\Translation\TranslatableEnumTrait;
use Elao\Enum\ExtrasTrait;

#[ReadableEnum(prefix: 'enum.blog_category.', useValueAsDefault: true)]
enum BlogCategoryEnum: string implements TranslatableEnumInterface
{
    use ExtrasTrait;
    use TranslatableEnumTrait;

    #[EnumCase(extras: [
        'color' => 'bg-ink-black/10 text-ink-black [a&]:hover:bg-ink-black/20',
    ])]
    case Ai = 'ai';

    #[EnumCase(extras: [
        'color' => 'bg-green-calm/10 text-green-calm [a&]:hover:bg-green-calm/20',
    ])]
    case Architecture = 'architecture';

    #[EnumCase(extras: [
        'color' => 'bg-dark-cyan/10 text-dark-cyan [a&]:hover:bg-dark-cyan/20',
    ])]
    case Career = 'career';

    #[EnumCase(extras: [
        'color' => 'bg-pearl-aqua/10 text-pearl-aqua [a&]:hover:bg-pearl-aqua/20',
    ])]
    case DevOps = 'dev-ops';

    #[EnumCase(extras: [
        'color' => 'bg-rusty-spice/10 text-rusty-spice [a&]:hover:bg-rusty-spice/20',
    ])]
    case Notes = 'notes';

    #[EnumCase(extras: [
        'color' => 'bg-dark-teal/10 text-dark-teal [a&]:hover:bg-dark-teal/20',
    ])]
    case Performance = 'performance';

    #[EnumCase(extras: [
        'color' => 'bg-oxidized-iron/10 text-oxidized-iron [a&]:hover:bg-oxidized-iron/20',
    ])]
    case Security = 'security';

    #[EnumCase(extras: [
        'color' => 'bg-burnt-caramel/10 text-burnt-caramel [a&]:hover:bg-burnt-caramel/20',
    ])]
    case Testing = 'testing';

    public function getColor(): string
    {
        return $this->getExtra('color');
    }
}
