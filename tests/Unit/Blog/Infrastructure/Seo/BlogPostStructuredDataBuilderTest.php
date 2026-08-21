<?php

declare(strict_types=1);

namespace App\Tests\Unit\Blog\Infrastructure\Seo;

use App\Blog\Domain\Enum\BlogCategoryEnum;
use App\Blog\Domain\Enum\BlogRouteNameEnum;
use App\Blog\Domain\ValueObject\BlogPost;
use App\Blog\Infrastructure\Seo\BlogPostStructuredDataBuilder;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Asset\Packages;
use Symfony\Component\Asset\PathPackage;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\UrlHelper;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class BlogPostStructuredDataBuilderTest extends TestCase
{
    private const string BASE_URL = 'https://talesfroma.dev';

    private BlogPostStructuredDataBuilder $builder;

    protected function setUp(): void
    {
        $requestStack = new RequestStack();
        $requestStack->push(Request::create(self::BASE_URL.'/blog/first-post'));

        $translator = $this->createStub(TranslatorInterface::class);
        $translator
            ->method('trans')
            ->willReturnCallback(static fn (string $id, array $parameters = [], ?string $domain = null, ?string $locale = null): string => match (true) {
                'app.author' === $id => 'Romain Monteil',
                'enum.blog_category.ai' === $id => 'fr' === $locale ? 'Ia' : 'Ai',
                default => $id,
            });

        $urlGenerator = $this->createStub(UrlGeneratorInterface::class);
        $urlGenerator
            ->method('generate')
            ->willReturnCallback(static function (string $name, array $parameters = []): string {
                if (BlogRouteNameEnum::WebsiteShow->value !== $name) {
                    return self::BASE_URL.'/';
                }

                $prefix = 'en' === $parameters['_locale'] ? '' : '/'.$parameters['_locale'];

                return \sprintf('%s%s/blog/%s', self::BASE_URL, $prefix, $parameters['slug']);
            });

        $this->builder = new BlogPostStructuredDataBuilder(
            assetPackages: new Packages(new PathPackage('/assets/', new EmptyVersionStrategy())),
            urlHelper: new UrlHelper($requestStack),
            translator: $translator,
            urlGenerator: $urlGenerator,
        );
    }

    public function testItBuildsBlogPostingForAPost(): void
    {
        $data = $this->builder->build($this->post());

        self::assertSame([
            '@context' => 'https://schema.org',
            '@type' => 'BlogPosting',
            'headline' => 'First post',
            'datePublished' => '2026-01-15',
            'inLanguage' => 'en',
            'articleSection' => 'Ai',
            'author' => [
                '@type' => 'Person',
                'name' => 'Romain Monteil',
                'url' => 'https://talesfroma.dev/',
            ],
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id' => 'https://talesfroma.dev/blog/first-post',
            ],
            'description' => 'The oldest published fixture.',
        ], $data);
    }

    public function testItPointsAtTheLocalisedUrlOfATranslatedPost(): void
    {
        $data = $this->builder->build($this->post(slug: 'premier-article', locale: 'fr'));

        self::assertSame('fr', $data['inLanguage']);
        self::assertSame('Ia', $data['articleSection']);
        self::assertSame(
            ['@type' => 'WebPage', '@id' => 'https://talesfroma.dev/fr/blog/premier-article'],
            $data['mainEntityOfPage'],
        );
    }

    public function testItAddsAnAbsoluteImageWhenThePostHasACover(): void
    {
        $data = $this->builder->build($this->post(cover: 'images/blog/cover.webp'));

        self::assertSame('https://talesfroma.dev/assets/images/blog/cover.webp', $data['image']);
    }

    public function testItOmitsOptionalFieldsWhenTheyAreAbsent(): void
    {
        $data = $this->builder->build($this->post(description: null));

        self::assertArrayNotHasKey('description', $data);
        self::assertArrayNotHasKey('image', $data);
    }

    private function post(
        string $slug = 'first-post',
        string $locale = 'en',
        ?string $description = 'The oldest published fixture.',
        ?string $cover = null,
    ): BlogPost {
        return new BlogPost(
            slug: $slug,
            publishedAt: new \DateTimeImmutable('2026-01-15'),
            locale: $locale,
            title: 'en' === $locale ? 'First post' : 'Premier article',
            description: $description,
            tags: ['php'],
            category: BlogCategoryEnum::Ai,
            translationKey: 'first-post',
            cover: $cover,
        );
    }
}
