<?php

declare(strict_types=1);

namespace App\Experience\Ui\Controller\Dashboard;

use App\Experience\Infrastructure\Repository\ExperienceRepository;
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
class IndexController extends AbstractController
{
    public function __construct(
        private readonly ExperienceRepository $experienceRepository,
    ) {
    }

    public function __invoke(): Response
    {
        return $this->render('app/dashboard/experience/index.html.twig', [
            'experiences' => $this->experienceRepository->findBy([], ['endAt' => Order::Descending->value]),
        ]);
    }
}
