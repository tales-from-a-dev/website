<?php

declare(strict_types=1);

namespace App\Ui\Controller\Admin\Security;

use App\Ui\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

#[Route(
    path: '/login',
    name: 'login',
    methods: [Request::METHOD_GET, Request::METHOD_POST]
)]
final class LoginController extends AbstractController
{
    public function __invoke(
        AuthenticationUtils $authenticationUtils,
        #[CurrentUser]
        ?UserInterface $user,
    ): Response {
        if ($user) {
            return $this->redirectToRoute('app_admin_dashboard_index');
        }

        return $this->render('admin/security/login.html.twig', [
            'last_error' => $authenticationUtils->getLastAuthenticationError(),
            'last_username' => $authenticationUtils->getLastUsername(),
        ]);
    }
}
