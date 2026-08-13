<?php

declare(strict_types=1);

namespace App\Blog\Infrastructure\EventListener;

use App\Blog\Domain\ValueObject\BlogPost;
use App\Blog\Infrastructure\Repository\BlogPostRepository;
use App\Blog\Infrastructure\Seo\BlogPostAlternateResolver;
use Presta\SitemapBundle\Event\SitemapPopulateEvent;
use Presta\SitemapBundle\Sitemap\Url\GoogleMultilangUrlDecorator;
use Presta\SitemapBundle\Sitemap\Url\UrlConcrete;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

/**
 * PrestaSitemap's route driver only registers parameterless routes, so the blog
 * index comes in through its `sitemap` route option while individual posts have
 * to be added here.
 */
#[AsEventListener(event: SitemapPopulateEvent::class)]
final readonly class BlogSitemapListener
{
    private const string SECTION = 'blog';

    private const float PRIORITY = 0.7;

    /**
     * @param list<string> $enabledLocales
     */
    public function __construct(
        private BlogPostRepository $blogPostRepository,
        private BlogPostAlternateResolver $blogPostAlternateResolver,
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
            foreach ($this->blogPostRepository->findAll($locale) as $post) {
                // findAll() keeps drafts visible under kernel.debug so they can
                // be previewed locally; they never belong in a sitemap.
                if ($post->draft) {
                    continue;
                }

                $urlContainer->addUrl($this->url($post), self::SECTION);
            }
        }
    }

    /**
     * Decorated the same way `alternate.i18n: symfony` decorates static routes,
     * so a post carries the same `xhtml:link` alternates as every other page.
     */
    private function url(BlogPost $post): GoogleMultilangUrlDecorator
    {
        $alternates = $this->blogPostAlternateResolver->resolve($post);

        $url = new GoogleMultilangUrlDecorator(
            new UrlConcrete(
                $alternates[$post->locale],
                $post->publishedAt,
                UrlConcrete::CHANGEFREQ_MONTHLY,
                self::PRIORITY,
            )
        );

        foreach ($alternates as $locale => $href) {
            $url->addLink($href, $locale);
        }

        return $url;
    }
}
