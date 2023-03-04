<?php

declare(strict_types=1);

namespace App\Ui\Twig\Components;

use App\Core\Enum\ColoreableEnumInterface;
use Elao\Enum\ReadableEnumInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'badge')]
final class BadgeComponent
{
    public ReadableEnumInterface&ColoreableEnumInterface $badge;
}
