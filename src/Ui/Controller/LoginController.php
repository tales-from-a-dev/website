<?php

declare(strict_types=1);

namespace App\Ui\Controller;

use App\Domain\Enum\RouteNameEnum;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

#[Route(
    path: '/login',
    name: RouteNameEnum::WebsiteLogin->value,
    methods: [
        Request::METHOD_GET,
        Request::METHOD_POST,
    ],
)]
final class LoginController extends AbstractController
{
    public function __invoke(
        Request $request,
        AuthenticationUtils $authenticationUtils,
    ): Response {
        return $this->render('app/website/login.html.twig', [
            'last_username' => $authenticationUtils->getLastUsername(),
            'last_error' => $authenticationUtils->getLastAuthenticationError(),
        ]);
    }
}
