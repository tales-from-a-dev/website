<?php

declare(strict_types=1);

namespace App\Shared\Ui\Twig\Extension;

use App\Shared\Ui\Twig\Runtime\SharedExtensionRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class SharedExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('is_active_path', [SharedExtensionRuntime::class, 'isActivePath']),
        ];
    }
}
