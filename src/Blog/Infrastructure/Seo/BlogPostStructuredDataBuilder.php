<?php

declare(strict_types=1);

namespace App\Blog\Infrastructure\Seo;

use App\Blog\Domain\Enum\BlogRouteNameEnum;
use App\Blog\Domain\ValueObject\BlogPost;
use App\Shared\Domain\Enum\SharedRouteNameEnum;
use Symfony\Component\Asset\Packages;
use Symfony\Component\HttpFoundation\UrlHelper;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class BlogPostStructuredDataBuilder
{
    public function __construct(
        private Packages $assetPackages,
        private UrlHelper $urlHelper,
        private TranslatorInterface $translator,
        private UrlGeneratorInterface $urlGenerator,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function build(BlogPost $post): array
    {
        $url = $this->urlGenerator->generate(
            BlogRouteNameEnum::WebsiteShow->value,
            [
                '_locale' => $post->locale,
                'slug' => $post->slug,
            ],
            UrlGeneratorInterface::ABSOLUTE_URL,
        );

        $data = [
            '@context' => 'https://schema.org',
            '@type' => 'BlogPosting',
            'headline' => $post->title,
            // The filename carries a date and no time, so the date is all that
            // can honestly be published.
            'datePublished' => $post->publishedAt->format('Y-m-d'),
            'inLanguage' => $post->locale,
            'author' => [
                '@type' => 'Person',
                'name' => $this->translator->trans('app.author'),
                'url' => $this->urlGenerator->generate(SharedRouteNameEnum::WebsiteIndex->value, [], UrlGeneratorInterface::ABSOLUTE_URL),
            ],
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id' => $url,
            ],
        ];

        if (null !== $post->description) {
            $data['description'] = $post->description;
        }

        if (null !== $post->cover) {
            $data['image'] = $this->urlHelper->getAbsoluteUrl($this->assetPackages->getUrl($post->cover));
        }

        return $data;
    }
}
