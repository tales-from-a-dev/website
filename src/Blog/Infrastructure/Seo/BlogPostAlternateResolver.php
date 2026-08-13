<?php

declare(strict_types=1);

namespace App\Blog\Infrastructure\Seo;

use App\Blog\Domain\Enum\BlogRouteNameEnum;
use App\Blog\Domain\ValueObject\BlogPost;
use App\Blog\Infrastructure\Repository\BlogPostRepository;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final readonly class BlogPostAlternateResolver
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
     * Absolute URL per locale the post is actually published in, keyed by
     * locale and always including the post's own — merging `_locale` into the
     * current route parameters would keep the source slug on every alternate,
     * and an alternate pointing at a 404 is worse than no alternate at all.
     *
     * @return array<string, string>
     */
    public function resolve(BlogPost $post): array
    {
        $alternates = [];

        foreach ($this->enabledLocales as $locale) {
            if ($locale === $post->locale) {
                $alternates[$locale] = $this->url($post);

                continue;
            }

            $counterpart = $this->blogPostRepository->findOneByTranslationKey($locale, $post->translationKey);
            if (null === $counterpart || $counterpart->draft) {
                continue;
            }

            $alternates[$locale] = $this->url($counterpart);
        }

        return $alternates;
    }

    private function url(BlogPost $post): string
    {
        return $this->urlGenerator->generate(
            BlogRouteNameEnum::WebsiteShow->value,
            [
                '_locale' => $post->locale,
                'slug' => $post->slug,
            ],
            UrlGeneratorInterface::ABSOLUTE_URL,
        );
    }
}
