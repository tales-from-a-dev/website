<?php

declare(strict_types=1);

namespace App\User\Ui\Component\Form;

use App\Shared\Ui\Controller\AbstractController;
use App\User\Domain\Enum\UserRouteNameEnum;
use App\User\Ui\Form\Type\LoginType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

#[AsTwigComponent]
final class LoginForm extends AbstractController
{
    public FormView $form;

    #[ExposeInTemplate(name: 'last_error')]
    public ?AuthenticationException $lastError = null;

    public function mount(string $lastUsername = ''): void
    {
        $this->form = $this->createLoginForm($lastUsername)->createView();
    }

    /**
     * @return FormInterface<mixed>
     */
    private function createLoginForm(string $lastUsername): FormInterface
    {
        return $this->createFormNamed(
            name: '',
            type: LoginType::class,
            data: [
                '_email' => $lastUsername,
            ],
            options: [
                'action' => $this->generateUrl(UserRouteNameEnum::DashboardLogin->value),
                'method' => Request::METHOD_POST,
            ]
        );
    }
}
