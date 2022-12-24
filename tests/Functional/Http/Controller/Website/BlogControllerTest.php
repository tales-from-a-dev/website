<?php

declare(strict_types=1);

namespace App\Tests\Functional\Http\Controller\Website;

use App\Tests\Factory\PostFactory;
use App\Tests\Factory\TagFactory;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;
use Zenstruck\Foundry\Test\Factories;

final class BlogControllerTest extends WebTestCase
{
    use Factories;

    private KernelBrowser $client;
    private TranslatorInterface $translator;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->translator = static::getContainer()->get(TranslatorInterface::class);
    }

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

        $crawler = $this->client->request(Request::METHOD_GET, $uri);

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

        $crawler = $this->client->request(Request::METHOD_GET, $uri);

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

        $this->client->request(Request::METHOD_GET, '/blog/dummy-title');

        self::assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    public function testItCanSearchPublishedPost(): void
    {
        PostFactory::new()
            ->published()
            ->sequence([
                ['title' => 'Dummy post 1'],
                ['title' => 'Dummy post 2'],
                ['title' => 'Dummy post 3'],
            ])
            ->create()
        ;

        self::ensureKernelShutdown();

        $crawler = $this->client->request(Request::METHOD_GET, '/blog');

        self::assertCount(3, $crawler->filter('article'));

        $crawler = $this->client->request(Request::METHOD_GET, '/blog?search=post+2');

        self::assertResponseIsSuccessful();
        self::assertCount(1, $crawler->filter('article'));
        self::assertSelectorTextContains('h2', 'Dummy post 2');
    }

    public function testItCanViewTagAndRelatedPosts(): void
    {
        $tag = TagFactory::new()->withName('Dummy tag')->create();

        PostFactory::new()->withSpecificTag($tag)->many(10)->create();

        self::ensureKernelShutdown();

        $crawler = $this->client->request(Request::METHOD_GET, "/blog/tag/{$tag->getSlug()}");

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', $this->translator->trans('tag.title', ['tag' => $tag]));
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
