<?php

declare(strict_types=1);

namespace App\Blog\Infrastructure\Seo;

use App\Blog\Domain\Enum\BlogCategoryEnum;
use App\Blog\Domain\Enum\BlogRouteNameEnum;
use App\Blog\Domain\ValueObject\BlogPost;
use App\Blog\Infrastructure\Repository\BlogPostRepository;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final readonly class BlogCategoryAlternateResolver
{
    /**
     * @param list<string> $enabledLocales
     */
    public function __construct(
        private BlogPostRepository $blogPostRepository,
        private UrlGeneratorInterface $urlGenerator,
        #[Autowire(value: '%app.enabled_locales%')] private array $enabledLocales,
    ) {
    }

    /**
     * Absolute URL per locale that actually has a post in this category. The
     * slug is locale invariant, so the generic `meta_hreflang` block would
     * advertise every locale, including the ones where the archive is a 404.
     *
     * @return array<string, string>
     */
    public function resolve(BlogCategoryEnum $category): array
    {
        $alternates = [];

        foreach ($this->enabledLocales as $locale) {
            $posts = array_filter(
                $this->blogPostRepository->findByCategory($locale, $category),
                static fn (BlogPost $post): bool => !$post->draft,
            );
            if ([] === $posts) {
                continue;
            }

            $alternates[$locale] = $this->urlGenerator->generate(
                BlogRouteNameEnum::WebsiteCategory->value,
                [
                    '_locale' => $locale,
                    'category' => $category->value,
                ],
                UrlGeneratorInterface::ABSOLUTE_URL,
            );
        }

        return $alternates;
    }
}
