<?php

declare(strict_types=1);

namespace App\Ui\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('is_active_path', [AppRuntime::class, 'isActivePath']),
        ];
    }
}
