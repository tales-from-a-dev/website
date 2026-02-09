<?php

declare(strict_types=1);

namespace App\Shared\Ui\Controller\Website;

use App\Experience\Infrastructure\Repository\ExperienceRepository;
use App\Settings\Infrastructure\Repository\SettingsRepository;
use App\Shared\Ui\Controller\AbstractController;
use Doctrine\Common\Collections\Order;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(
    path: '/',
    name: 'index',
    methods: [
        Request::METHOD_GET,
    ]
)]
final class IndexController extends AbstractController
{
    public function __invoke(
        ExperienceRepository $experienceRepository,
        SettingsRepository $settingsRepository,
    ): Response {
        return $this->render('app/website/index.html.twig', [
            'experiences' => $experienceRepository->findBy([], ['endAt' => Order::Descending->value]),
            'settings' => $settingsRepository->findFirst(),
        ]);
    }
}
