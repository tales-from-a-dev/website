<?php

declare(strict_types=1);

namespace App\Shared\Ui\Twig\Runtime;

use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\RuntimeExtensionInterface;

final readonly class SharedExtensionRuntime implements RuntimeExtensionInterface
{
    public function __construct(
        private RequestStack $requestStack,
    ) {
    }

    /**
     * @param string|string[]      $paths
     * @param array<string, mixed> $parameters
     */
    public function isActivePath(string|array $paths, bool $startsWith = false, array $parameters = []): bool
    {
        if (\is_string($paths)) {
            $paths = [$paths];
        }

        if (
            (null === $currentRequest = $this->requestStack->getCurrentRequest()) ||
            (null === $currentRoute = $currentRequest->attributes->get('_route'))
        ) {
            return false;
        }

        $currentRouteParameters = $currentRequest->attributes->get('_route_params', []);

        foreach ($paths as $path) {
            if ($currentRoute === $path || ($startsWith && str_starts_with((string) $currentRoute, (string) $path))) {
                if ([] !== $parameters) {
                    return array_filter($currentRouteParameters) == $parameters;
                }

                return true;
            }
        }

        return false;
    }
}
