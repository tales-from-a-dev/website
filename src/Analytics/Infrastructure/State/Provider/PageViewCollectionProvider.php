<?php

declare(strict_types=1);

namespace App\Analytics\Infrastructure\State\Provider;

use App\Analytics\Domain\Entity\PageView;
use App\Analytics\Domain\Repository\PageViewRepositoryInterface;
use App\Shared\Domain\State\ProviderInterface;

/**
 * @implements ProviderInterface<PageView>
 */
final readonly class PageViewCollectionProvider implements ProviderInterface
{
    public function __construct(
        private PageViewRepositoryInterface $pageViewRepository,
    ) {
    }

    /**
     * @param array{
     *     pagination?: array{
     *         limit: int,
     *     }
     * } $context
     *
     * @return PageView[]
     */
    public function provide(array $context = []): array
    {
        $limit = $context['pagination']['limit'] ?? 10;

        return $this->pageViewRepository->findLatest($limit);
    }
}
