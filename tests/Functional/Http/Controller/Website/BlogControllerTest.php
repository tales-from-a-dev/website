<?php

declare(strict_types=1);

namespace App\Tests\Functional\Http\Controller\Website;

use App\Domain\Blog\Factory\PostFactory;
use App\Domain\Blog\Factory\TagFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;
use Zenstruck\Foundry\Test\Factories;

final class BlogControllerTest extends WebTestCase
{
    use Factories;

    /**
     * @dataProvider getIndexUri
     */
    public function testItCanViewIndexPage(string $uri): void
    {
        PostFactory::new()
            ->published()
            ->many(10)
            ->create()
        ;

        self::ensureKernelShutdown();

        $client = static::createClient();
        $crawler = $client->request(Request::METHOD_GET, $uri);

        self::assertResponseIsSuccessful();
        self::assertCount(5, $crawler->filter('article'));
    }

    /**
     * @dataProvider getShowUri
     */
    public function testItCanViewPublishedPost(string $uri): void
    {
        $post = PostFactory::new()->published()->withTitle('Dummy Post')->create();

        self::ensureKernelShutdown();

        $client = static::createClient();
        $crawler = $client->request(Request::METHOD_GET, $uri);

        self::assertResponseIsSuccessful();
        self::assertCount(1, $crawler->filter('article'));
        self::assertSelectorTextContains('h1', $post->getTitle());
    }

    /**
     * @dataProvider getUnpublishedPosts
     */
    public function testItCanNotViewUnpublishedPost(string $states): void
    {
        PostFactory::new(states: $states)->withTitle('Dummy title')->create();

        self::ensureKernelShutdown();

        $client = static::createClient();
        $client->request(Request::METHOD_GET, '/blog/dummy-title');

        self::assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    public function testItCanViewTagAndRelatedPosts(): void
    {
        $tag = TagFactory::new()->withName('Dummy tag')->create();

        PostFactory::new()->withSpecificTag($tag)->many(10)->create();

        self::ensureKernelShutdown();

        $client = static::createClient();
        $crawler = $client->request(Request::METHOD_GET, "/blog/tag/{$tag->getSlug()}");

        $translator = static::getContainer()->get(TranslatorInterface::class);

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', $translator->trans('tag.title', ['tag' => $tag]));
        self::assertCount(10, $crawler->filter('article'));
    }

    public static function getIndexUri(): \Generator
    {
        yield ['/blog'];
        yield ['/en/blog'];
    }

    public static function getShowUri(): \Generator
    {
        yield ['/blog/dummy-post'];
        yield ['/en/blog/dummy-post'];
    }

    public static function getUnpublishedPosts(): iterable
    {
        yield ['draft'];
        yield ['frozen'];
        yield ['publishedInFuture'];
    }
}
