<?php

declare(strict_types=1);

namespace App\Ui\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class AppExtension extends AbstractExtension
{
    #[\Override]
    public function getFunctions(): array
    {
        return [
            new TwigFunction('is_active_path', [AppRuntime::class, 'isActivePath']),
        ];
    }
}
