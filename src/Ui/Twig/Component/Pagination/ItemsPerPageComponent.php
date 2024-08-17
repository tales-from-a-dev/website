<?php

declare(strict_types=1);

namespace App\Ui\Twig\Component\Pagination;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

#[AsTwigComponent(name: 'pagination:items_per_page')]
final class ItemsPerPageComponent
{
    final public const string DISPLAY_SHORT = 'short';
    final public const string DISPLAY_FULL = 'full';

    public int $currentItemsPerPage = 10;
    public string $display = self::DISPLAY_SHORT;

    private int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH;

    /**
     * @param array<int> $allowedItemsPerPage
     */
    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly array $allowedItemsPerPage = [],
    ) {
        if (null !== $currentRequest = $this->requestStack->getCurrentRequest()) {
            $this->currentItemsPerPage = $currentRequest->query->getInt('limit', 10);
        }
    }

    public function mount(bool $absoluteUrl = false): void
    {
        if ($absoluteUrl) {
            $this->referenceType = UrlGeneratorInterface::ABSOLUTE_URL;
        }
    }

    /**
     * @return iterable<int, string>
     */
    #[ExposeInTemplate]
    public function getItemsPerPageUrls(): iterable
    {
        $currentRequest = $this->requestStack->getCurrentRequest();

        $route = $currentRequest?->attributes->get('_route');
        $parameters = array_merge(
            $currentRequest?->attributes->get('_route_params', []),
            $currentRequest?->query->all() ?? [],
        );

        $itemsPerPageUrls = [];
        foreach ($this->allowedItemsPerPage as $limit) {
            $parameters['limit'] = $limit;

            $itemsPerPageUrls[$limit] = $this->urlGenerator->generate($route, $parameters, $this->referenceType);
        }

        return $itemsPerPageUrls;
    }
}
