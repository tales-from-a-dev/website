<?php

declare(strict_types=1);

namespace App\Ui\Controller;

use App\Domain\Enum\RouteNameEnum;
use App\Infrastructure\Repository\ExperienceRepository;
use App\Infrastructure\Repository\SettingsRepository;
use Doctrine\Common\Collections\Order;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(
    path: '/',
    name: RouteNameEnum::WebsiteHome->value,
    methods: [
        Request::METHOD_GET,
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
