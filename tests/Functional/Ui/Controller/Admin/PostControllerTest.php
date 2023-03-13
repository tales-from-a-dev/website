<?php

declare(strict_types=1);

namespace App\Tests\Functional\Ui\Controller\Admin;

use App\Core\Enum\Role;
use App\Domain\Blog\Enum\PublicationStatus;
use App\Tests\Factory\PostFactory;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\InMemoryUser;
use Symfony\Contracts\Translation\TranslatorInterface;
use Zenstruck\Foundry\Test\Factories;

class PostControllerTest extends WebTestCase
{
    use Factories;

    private KernelBrowser $client;
    private TranslatorInterface $translator;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->client->loginUser(new InMemoryUser(
            username: $this->getContainer()->getParameter('user.email'),
            password: $this->getContainer()->getParameter('user.password'),
            roles: [Role::User->value]
        ));
        $this->translator = static::getContainer()->get(TranslatorInterface::class);
    }

    public function testItCanViewIndexPage(): void
    {
        PostFactory::createMany(20);

        self::ensureKernelShutdown();

        $crawler = $this->client->request(Request::METHOD_GET, '/admin/post');

        self::assertResponseIsSuccessful();
        self::assertPageTitleSame(sprintf('%s | %s', $this->translator->trans(id: 'crud.list.title', parameters: ['entity_name' => 'post'], domain: 'admin'), $this->translator->trans('app.meta.title')));
        self::assertCount(10, $crawler->filter('table > tbody > tr'));
    }

    public function testItCanCreatePost(): void
    {
        $post = PostFactory::new()->withoutPersisting()->create();

        PostFactory::assert()->empty();

        self::ensureKernelShutdown();

        $this->client->request(Request::METHOD_GET, '/admin/post/new');
        $this->client->submitForm('submit', [
            'post[publicationStatus]' => $post->getPublicationStatus()->value,
            'post[publishedAt]' => $post->getPublishedAt(),
            'post[title]' => $post->getTitle(),
            'post[content]' => $post->getContent(),
        ]);
        $crawler = $this->client->followRedirect();

        self::assertResponseIsSuccessful();
        self::assertCount(1, $crawler->filter('table > tbody > tr'));
        self::assertCount(1, $crawler->filter('div[role=alert]'));
        self::assertSelectorTextContains('div[role=alert] > p', $this->translator->trans(id: 'new.success', parameters: ['selector' => $post->getEntityName(), 'name' => $post->getTitle()], domain: 'alert'));

        PostFactory::assert()->count(1, ['title' => $post->getTitle()]);
    }

    public function testItCanEditPost(): void
    {
        $post = PostFactory::createOne();

        PostFactory::assert()->count(1);

        self::ensureKernelShutdown();

        $this->client->request(Request::METHOD_GET, "/admin/post/{$post->getId()}/edit");
        $crawler = $this->client->submitForm('submit', [
            'post[title]' => $postTitle = 'Dummy title',
        ]);

        self::assertResponseIsSuccessful();
        self::assertCount(1, $crawler->filter('div[role=alert]'));
        self::assertSelectorTextContains('div[role=alert] > p', $this->translator->trans(id: 'edit.success', parameters: ['selector' => $post->getEntityName(), 'name' => $post->getTitle()], domain: 'alert'));

        PostFactory::assert()->count(1, ['title' => $postTitle]);
    }

    public function testItCanDeletePost(): void
    {
        $post = PostFactory::createOne();

        PostFactory::assert()->count(1);

        self::ensureKernelShutdown();

        $this->client->request(Request::METHOD_GET, '/admin/post');
        $this->client->submitForm("submit_delete_{$post->getId()}");
        $crawler = $this->client->followRedirect();

        self::assertResponseIsSuccessful();
        self::assertCount(0, $crawler->filter('table'));
        self::assertCount(1, $crawler->filter('div[role=alert]'));
        self::assertSelectorTextContains('div[role=alert] > p', $this->translator->trans(id: 'delete.success', parameters: ['selector' => $post->getEntityName(), 'name' => $post->getTitle()], domain: 'alert'));

        PostFactory::assert()->empty();
    }

    public function testItTriggerErrorsWithEmptyData(): void
    {
        $this->client->request(Request::METHOD_GET, '/admin/post/new');
        $crawler = $this->client->submitForm('submit', [
            'post[publicationStatus]' => PublicationStatus::Draft->value,
            'post[title]' => '',
            'post[content]' => '',
        ]);

        self::assertResponseIsUnprocessable();
        self::assertCount(1, $crawler->filter('input[id=post_title] + p'));
        self::assertSelectorTextContains('input[id=post_title] + p', $this->translator->trans(id: 'This value should not be blank.', domain: 'validators'));
        self::assertCount(1, $crawler->filter('textarea[id=post_content] + p'));
        self::assertSelectorTextContains('textarea[id=post_content] + p', $this->translator->trans(id: 'This value should not be blank.', domain: 'validators'));
    }

    public function testItTriggerErrorsWithTooShortTitle(): void
    {
        $this->client->request(Request::METHOD_GET, '/admin/post/new');
        $crawler = $this->client->submitForm('submit', [
            'post[title]' => 'dumm',
        ]);

        self::assertResponseIsUnprocessable();
        self::assertCount(1, $crawler->filter('input[id=post_title] + p'));
        self::assertSelectorTextContains('input[id=post_title] + p', $this->translator->trans(id: 'This value is too short. It should have {{ limit }} character or more.|This value is too short. It should have {{ limit }} characters or more.', parameters: ['{{ limit }}' => 5, '%count%' => 2], domain: 'validators'));
    }

    public function testItTriggerErrorsWithExistingTitle(): void
    {
        PostFactory::createOne(['title' => 'Dummy title']);

        self::ensureKernelShutdown();

        $this->client->request(Request::METHOD_GET, '/admin/post/new');
        $crawler = $this->client->submitForm('submit', [
            'post[publicationStatus]' => PublicationStatus::Draft->value,
            'post[publishedAt]' => (new \DateTimeImmutable('tomorrow'))->format(\DateTimeImmutable::ATOM),
            'post[title]' => 'Dummy title',
            'post[content]' => 'Dummy content',
        ]);

        self::assertResponseIsUnprocessable();
        self::assertCount(1, $crawler->filter('input[id=post_title] + p'));
        self::assertSelectorTextContains('input[id=post_title] + p', $this->translator->trans(id: 'This value is already used.', domain: 'validators'));
    }
}
