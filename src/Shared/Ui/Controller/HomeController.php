<?php

declare(strict_types=1);

namespace App\Shared\Ui\Controller;

use App\Experience\Infrastructure\Repository\ExperienceRepository;
use App\Settings\Infrastructure\Repository\SettingsRepository;
use Doctrine\Common\Collections\Order;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(
    path: '/',
    name: 'home',
    methods: [
        Request::METHOD_GET,
        Request::METHOD_POST,
    ]
)]
final class HomeController extends AbstractController
{
    public function __invoke(
        ExperienceRepository $experienceRepository,
        SettingsRepository $settingsRepository,
    ): Response {
        return $this->render('app/website/home.html.twig', [
            'experiences' => $experienceRepository->findBy([], ['startAt' => Order::Descending->value]),
            'settings' => $settingsRepository->findFirst(),
        ]);
    }
}
