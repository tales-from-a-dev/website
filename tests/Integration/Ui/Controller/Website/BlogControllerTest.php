<?php

declare(strict_types=1);

namespace App\Tests\Integration\Ui\Controller\Website;

use App\Domain\Blog\Enum\PublicationStatus;
use App\Tests\Factory\PostFactory;
use App\Tests\Factory\TagFactory;
use PHPUnit\Framework\Attributes\DataProvider;
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

    #[\Override]
    protected function setUp(): void
    {
        $this->client = self::createClient();
        $this->translator = self::getContainer()->get(TranslatorInterface::class);
    }

    #[DataProvider('getIndexUri')]
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

    #[DataProvider('getShowUri')]
    public function testItCanViewPublishedPost(string $uri): void
    {
        $post = PostFactory::new()->published()->withTitle('Dummy Post')->create();

        self::ensureKernelShutdown();

        $crawler = $this->client->request(Request::METHOD_GET, $uri);

        self::assertResponseIsSuccessful();
        self::assertCount(1, $crawler->filter('article'));
        self::assertSelectorTextContains('h1', $post->getTitle());
    }

    #[DataProvider('getUnpublishedPosts')]
    public function testItCanNotViewUnpublishedPost(string $states): void
    {
        PostFactory::new()
            ->withPublicationStatus($status)
            ->withTitle('Dummy title')
            ->create()
        ;

        self::ensureKernelShutdown();

        $this->client->request(Request::METHOD_GET, '/blog/dummy-title');

        self::assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testItCanNotViewPublishedInFuturePost(): void
    {
        PostFactory::new()
            ->publishedInFuture()
            ->withTitle('Dummy title')
            ->create()
        ;

        self::ensureKernelShutdown();

        $this->client->request(Request::METHOD_GET, '/blog/dummy-title');
        self::assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
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

    /**
     * @return iterable<array<int, mixed>>
     */
    public static function getIndexUri(): iterable
    {
        yield ['/blog'];
    }

    /**
     * @return iterable<array<int, mixed>>
     */
    public static function getShowUri(): iterable
    {
        yield ['/blog/dummy-post'];
    }

    /**
     * @return iterable<array<int, mixed>>
     */
    public static function getUnpublishedPosts(): iterable
    {
        yield [PublicationStatus::Draft];
        yield [PublicationStatus::Frozen];
    }
}
