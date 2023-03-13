<?php

declare(strict_types=1);

namespace App\Tests\Functional\Ui\Controller\Admin;

use App\Core\Enum\Role;
use App\Tests\Factory\TagFactory;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\InMemoryUser;
use Symfony\Contracts\Translation\TranslatorInterface;
use Zenstruck\Foundry\Test\Factories;

class TagControllerTest extends WebTestCase
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
        TagFactory::createMany(20);

        self::ensureKernelShutdown();

        $crawler = $this->client->request(Request::METHOD_GET, '/admin/tag');

        self::assertResponseIsSuccessful();
        self::assertPageTitleSame(sprintf('%s | %s', $this->translator->trans(id: 'crud.list.title', parameters: ['entity_name' => 'tag'], domain: 'admin'), $this->translator->trans('app.meta.title')));
        self::assertCount(10, $crawler->filter('table > tbody > tr'));
    }

    public function testItCanCreateTag(): void
    {
        $tag = TagFactory::new()->withoutPersisting()->create();

        TagFactory::assert()->empty();

        self::ensureKernelShutdown();

        $this->client->request(Request::METHOD_GET, '/admin/tag/new');
        $this->client->submitForm('submit', [
            'tag[name]' => $tag->getName(),
        ]);
        $crawler = $this->client->followRedirect();

        self::assertResponseIsSuccessful();
        self::assertCount(1, $crawler->filter('table > tbody > tr'));
        self::assertCount(1, $crawler->filter('div[role=alert]'));
        self::assertSelectorTextContains('div[role=alert] > p', $this->translator->trans(id: 'new.success', parameters: ['selector' => $tag->getEntityName(), 'name' => $tag->getName()], domain: 'alert'));

        TagFactory::assert()->count(1, ['name' => $tag->getName()]);
    }

    public function testItCanEditTag(): void
    {
        $tag = TagFactory::createOne();

        TagFactory::assert()->count(1);

        self::ensureKernelShutdown();

        $this->client->request(Request::METHOD_GET, "/admin/tag/{$tag->getId()}/edit");
        $crawler = $this->client->submitForm('submit', [
            'tag[name]' => $tagName = 'Dummy tag',
        ]);

        self::assertResponseIsSuccessful();
        self::assertCount(1, $crawler->filter('div[role=alert]'));
        self::assertSelectorTextContains('div[role=alert] > p', $this->translator->trans(id: 'edit.success', parameters: ['selector' => $tag->getEntityName(), 'name' => $tag->getName()], domain: 'alert'));

        TagFactory::assert()->count(1, ['name' => $tagName]);
    }

    public function testItCanDeleteTag(): void
    {
        $tag = TagFactory::createOne();

        TagFactory::assert()->count(1);

        self::ensureKernelShutdown();

        $this->client->request(Request::METHOD_GET, '/admin/tag');
        $this->client->submitForm("submit_delete_{$tag->getId()}");
        $crawler = $this->client->followRedirect();

        self::assertResponseIsSuccessful();
        self::assertCount(0, $crawler->filter('table'));
        self::assertCount(1, $crawler->filter('div[role=alert]'));
        self::assertSelectorTextContains('div[role=alert] > p', $this->translator->trans(id: 'delete.success', parameters: ['selector' => $tag->getEntityName(), 'name' => $tag->getName()], domain: 'alert'));

        TagFactory::assert()->empty();
    }

    public function testItTriggerErrorsWithEmptyName(): void
    {
        $this->client->request(Request::METHOD_GET, '/admin/tag/new');
        $crawler = $this->client->submitForm('submit', [
            'tag[name]' => '',
        ]);

        self::assertResponseIsUnprocessable();
        self::assertCount(1, $crawler->filter('input[id=tag_name] + p'));
        self::assertSelectorTextContains('input[id=tag_name] + p', $this->translator->trans(id: 'This value should not be blank.', domain: 'validators'));
    }

    public function testItTriggerErrorsWithTooShortName(): void
    {
        $this->client->request(Request::METHOD_GET, '/admin/tag/new');
        $crawler = $this->client->submitForm('submit', [
            'tag[name]' => 'd',
        ]);

        self::assertResponseIsUnprocessable();
        self::assertCount(1, $crawler->filter('input[id=tag_name] + p'));
        self::assertSelectorTextContains('input[id=tag_name] + p', $this->translator->trans(id: 'This value is too short. It should have {{ limit }} character or more.|This value is too short. It should have {{ limit }} characters or more.', parameters: ['{{ limit }}' => 2, '%count%' => 2], domain: 'validators'));
    }

    public function testItTriggerErrorsWithExistingName(): void
    {
        TagFactory::createOne(['name' => 'php']);

        self::ensureKernelShutdown();

        $this->client->request(Request::METHOD_GET, '/admin/tag/new');
        $crawler = $this->client->submitForm('submit', [
            'tag[name]' => 'php',
        ]);

        self::assertResponseIsUnprocessable();
        self::assertCount(1, $crawler->filter('input[id=tag_name] + p'));
        self::assertSelectorTextContains('input[id=tag_name] + p', $this->translator->trans(id: 'This value is already used.', domain: 'validators'));
    }
}
