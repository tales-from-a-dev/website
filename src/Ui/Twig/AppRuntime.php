<?php

declare(strict_types=1);

namespace App\Ui\Twig;

use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\RuntimeExtensionInterface;

final readonly class AppRuntime implements RuntimeExtensionInterface
{
    public function __construct(
        private RequestStack $requestStack,
    ) {
    }

    /**
     * Check if current route is the same as "$path".
     *
     * @param string|array<string> $paths
     */
    public function isActivePath(string|array $paths, bool $startsWith = false, string $class = 'active'): ?string
    {
        if (\is_string($paths)) {
            $paths = [$paths];
        }

        $currentRoute = $this->requestStack->getCurrentRequest()?->attributes->get('_route');
        foreach ($paths as $path) {
            if ($currentRoute && (($currentRoute === $path) || ($startsWith && str_starts_with((string) $currentRoute, (string) $path)))) {
                return ' '.$class;
            }
        }

        return null;
    }
}
