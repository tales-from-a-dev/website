<?php

declare(strict_types=1);

namespace App\Tests\Functional\Ui\Controller\Admin;

use App\Core\Enum\Role;
use App\Domain\Project\Enum\ProjectType;
use App\Tests\Factory\ProjectFactory;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\InMemoryUser;
use Symfony\Contracts\Translation\TranslatorInterface;
use Zenstruck\Foundry\Test\Factories;

class ProjectControllerTest extends WebTestCase
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
        ProjectFactory::createMany(20);

        self::ensureKernelShutdown();

        $crawler = $this->client->request(Request::METHOD_GET, '/admin/project');

        self::assertResponseIsSuccessful();
        self::assertPageTitleSame(sprintf('%s | %s', $this->translator->trans(id: 'crud.list.title', parameters: ['entity_name' => 'project'], domain: 'admin'), $this->translator->trans('app.meta.title')));
        self::assertCount(10, $crawler->filter('table > tbody > tr'));
    }

    public function testItCanCreateProject(): void
    {
        $project = ProjectFactory::new()->withoutPersisting()->create();

        ProjectFactory::assert()->empty();

        self::ensureKernelShutdown();

        $this->client->request(Request::METHOD_GET, '/admin/project/new');
        $this->client->submitForm('submit', [
            'project[title]' => $project->getTitle(),
            'project[subTitle]' => $project->getSubTitle(),
            'project[description]' => $project->getDescription(),
            'project[type]' => $project->getType()->value,
        ]);
        $crawler = $this->client->followRedirect();

        self::assertResponseIsSuccessful();
        self::assertCount(1, $crawler->filter('table > tbody > tr'));
        self::assertCount(1, $crawler->filter('div[role=alert]'));
        self::assertSelectorTextContains('div[role=alert] > p', $this->translator->trans(id: 'new.success', parameters: ['selector' => $project->getEntityName(), 'name' => $project->getTitle()], domain: 'alert'));

        ProjectFactory::assert()->count(1, ['title' => $project->getTitle()]);
    }

    public function testItCanEditProject(): void
    {
        $project = ProjectFactory::createOne();

        ProjectFactory::assert()->count(1);

        self::ensureKernelShutdown();

        $this->client->request(Request::METHOD_GET, "/admin/project/{$project->getId()}/edit");
        $crawler = $this->client->submitForm('submit', [
            'project[title]' => $projectTitle = 'Dummy title',
        ]);

        self::assertResponseIsSuccessful();
        self::assertCount(1, $crawler->filter('div[role=alert]'));
        self::assertSelectorTextContains('div[role=alert] > p', $this->translator->trans(id: 'edit.success', parameters: ['selector' => $project->getEntityName(), 'name' => $project->getTitle()], domain: 'alert'));

        ProjectFactory::assert()->count(1, ['title' => $projectTitle]);
    }

    public function testItCanDeleteProject(): void
    {
        $project = ProjectFactory::createOne();

        ProjectFactory::assert()->count(1);

        self::ensureKernelShutdown();

        $this->client->request(Request::METHOD_GET, '/admin/project');
        $this->client->submitForm("submit_delete_{$project->getId()}");
        $crawler = $this->client->followRedirect();

        self::assertResponseIsSuccessful();
        self::assertCount(0, $crawler->filter('table'));
        self::assertCount(1, $crawler->filter('div[role=alert]'));
        self::assertSelectorTextContains('div[role=alert] > p', $this->translator->trans(id: 'delete.success', parameters: ['selector' => $project->getEntityName(), 'name' => $project->getTitle()], domain: 'alert'));

        ProjectFactory::assert()->empty();
    }

    public function testItTriggerErrorsWithEmptyData(): void
    {
        $this->client->request(Request::METHOD_GET, '/admin/project/new');
        $crawler = $this->client->submitForm('submit', [
            'project[title]' => '',
            'project[subTitle]' => '',
            'project[description]' => '',
            'project[type]' => ProjectType::Customer->value,
        ]);

        self::assertResponseIsUnprocessable();
        self::assertCount(1, $crawler->filter('input[id=project_title] + p'));
        self::assertSelectorTextContains('input[id=project_title] + p', $this->translator->trans(id: 'This value should not be blank.', domain: 'validators'));
        self::assertCount(1, $crawler->filter('textarea[id=project_description] + p'));
        self::assertSelectorTextContains('textarea[id=project_description] + p', $this->translator->trans(id: 'This value should not be blank.', domain: 'validators'));
    }

    public function testItTriggerErrorsWithTooShortTitle(): void
    {
        $this->client->request(Request::METHOD_GET, '/admin/project/new');
        $crawler = $this->client->submitForm('submit', [
            'project[title]' => 'dumm',
        ]);

        self::assertResponseIsUnprocessable();
        self::assertCount(1, $crawler->filter('input[id=project_title] + p'));
        self::assertSelectorTextContains('input[id=project_title] + p', $this->translator->trans(id: 'This value is too short. It should have {{ limit }} character or more.|This value is too short. It should have {{ limit }} characters or more.', parameters: ['{{ limit }}' => 5, '%count%' => 2], domain: 'validators'));
    }

    public function testItTriggerErrorsWithExistingTitle(): void
    {
        ProjectFactory::createOne(['title' => 'Dummy title']);

        self::ensureKernelShutdown();

        $this->client->request(Request::METHOD_GET, '/admin/project/new');
        $crawler = $this->client->submitForm('submit', [
            'project[title]' => 'Dummy title',
            'project[subTitle]' => 'Dummy subtitle',
            'project[description]' => 'Dummy description',
            'project[type]' => ProjectType::Customer->value,
        ]);

        self::assertResponseIsUnprocessable();
        self::assertCount(1, $crawler->filter('input[id=project_title] + p'));
        self::assertSelectorTextContains('input[id=project_title] + p', $this->translator->trans(id: 'This value is already used.', domain: 'validators'));
    }
}
