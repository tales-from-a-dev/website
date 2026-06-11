<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\EventListener;

use App\Shared\Domain\Enum\SharedRouteNameEnum;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

use function Symfony\Component\String\u;

#[AsEventListener(event: KernelEvents::REQUEST, priority: 8)]
final readonly class RedirectToPreferredLocale
{
    /**
     * @param list<string> $enabledLocales
     */
    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
        #[Autowire(value: '%app.enabled_locales%')] private array $enabledLocales,
    ) {
    }

    public function __invoke(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        $route = $request->attributes->get('_route');

        if (!\is_string($route) || $route !== SharedRouteNameEnum::WebsiteIndex->value) {
            return;
        }

        $referrer = $request->headers->get('referer');

        if (null !== $referrer && u($referrer)->ignoreCase()->startsWith($request->getSchemeAndHttpHost())) {
            return;
        }

        $preferredLanguage = $request->getPreferredLanguage($this->enabledLocales);

        if ($preferredLanguage !== $request->getLocale()) {
            $response = new RedirectResponse($this->urlGenerator->generate(SharedRouteNameEnum::WebsiteIndex->value, ['_locale' => $preferredLanguage]));

            $event->setResponse($response);
        }
    }
}
