<?php

declare(strict_types=1);

namespace App\Blog\Infrastructure\EventListener;

use App\Blog\Domain\ValueObject\BlogPost;
use App\Blog\Infrastructure\Repository\BlogPostRepository;
use App\Blog\Infrastructure\Seo\BlogCategoryAlternateResolver;
use App\Blog\Infrastructure\Seo\BlogPostAlternateResolver;
use Presta\SitemapBundle\Event\SitemapPopulateEvent;
use Presta\SitemapBundle\Sitemap\Url\GoogleMultilangUrlDecorator;
use Presta\SitemapBundle\Sitemap\Url\UrlConcrete;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

/**
 * PrestaSitemap's route driver only registers parameterless routes, so the blog
 * index comes in through its `sitemap` route option while individual posts and
 * category archives have to be added here. Tag archives stay out: they are
 * `noindex, follow`.
 */
#[AsEventListener(event: SitemapPopulateEvent::class)]
final readonly class BlogSitemapListener
{
    private const string SECTION = 'blog';

    private const float POST_PRIORITY = 0.7;

    private const float CATEGORY_PRIORITY = 0.6;

    /**
     * @param list<string> $enabledLocales
     */
    public function __construct(
        private BlogPostRepository $blogPostRepository,
        private BlogPostAlternateResolver $blogPostAlternateResolver,
        private BlogCategoryAlternateResolver $blogCategoryAlternateResolver,
        #[Autowire(value: '%app.enabled_locales%')] private array $enabledLocales,
    ) {
    }

    public function __invoke(SitemapPopulateEvent $event): void
    {
        $section = $event->getSection();
        if (null !== $section && self::SECTION !== $section) {
            return;
        }

        $urlContainer = $event->getUrlContainer();

        foreach ($this->enabledLocales as $locale) {
            $newestPerCategory = [];

            foreach ($this->blogPostRepository->findAll($locale) as $post) {
                if ($post->draft) {
                    continue;
                }

                $urlContainer->addUrl($this->postUrl($post), self::SECTION);

                $newestPerCategory[$post->category->value] ??= $post;
            }

            foreach ($newestPerCategory as $post) {
                $urlContainer->addUrl($this->categoryUrl($locale, $post), self::SECTION);
            }
        }
    }

    /**
     * Decorated the same way `alternate.i18n: symfony` decorates static routes,
     * so a post carries the same `xhtml:link` alternates as every other page.
     */
    private function postUrl(BlogPost $post): GoogleMultilangUrlDecorator
    {
        return $this->url(
            $this->blogPostAlternateResolver->resolve($post),
            $post->locale,
            $post->publishedAt,
            UrlConcrete::CHANGEFREQ_MONTHLY,
            self::POST_PRIORITY,
        );
    }

    private function categoryUrl(string $locale, BlogPost $post): GoogleMultilangUrlDecorator
    {
        return $this->url(
            $this->blogCategoryAlternateResolver->resolve($post->category),
            $locale,
            $post->publishedAt,
            UrlConcrete::CHANGEFREQ_WEEKLY,
            self::CATEGORY_PRIORITY,
        );
    }

    /**
     * @param array<string, string> $alternates
     */
    private function url(
        array $alternates,
        string $locale,
        \DateTimeInterface $lastmod,
        string $changefreq,
        float $priority,
    ): GoogleMultilangUrlDecorator {
        $url = new GoogleMultilangUrlDecorator(
            new UrlConcrete($alternates[$locale], $lastmod, $changefreq, $priority)
        );

        foreach ($alternates as $alternateLocale => $href) {
            $url->addLink($href, $alternateLocale);
        }

        return $url;
    }
}
