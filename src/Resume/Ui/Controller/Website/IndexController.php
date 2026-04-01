<?php

declare(strict_types=1);

namespace App\Resume\Ui\Controller\Website;

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
final class IndexController extends AbstractController
{
    public function __construct(
        private readonly ExperienceRepository $experienceRepository,
    ) {
    }

    public function __invoke(): Response
    {
        return $this->render('app/website/resume/index.html.twig', [
            'experiences' => $this->experienceRepository->findBy([], ['endAt' => Order::Descending->value], 5),
            'skills' => [
                'backend' => ['PHP', 'Symfony', 'Symfony UX', 'Laravel', 'API Platform', 'PHPUnit', 'PHPStan'],
                'frontend' => ['HTML', 'CSS', 'JavaScript', 'TypeScript', 'Tailwind CSS', 'Stimulus', 'Turbo', 'Twig'],
                'devops' => ['Docker', 'CI/CD', 'GitHub Actions', 'FrankenPHP'],
                'architecture' => ['Hexagonal', 'DDD', 'CQRS', 'Event Sourcing'],
                'methodology' => ['Scrum', 'Kanban'],
            ],
            'certifications' => [
                [
                    'name' => 'Twig 3 Certified Designer',
                ],
            ],
            'educations' => [
                [
                    'school' => 'IUT de Bayonne',
                    'place' => 'Anglet',
                    'title' => 'LP SIL',
                    'description' => 'Spécialisation développement d’applications distribuées orientées Web',
                    'startAt' => '2009',
                    'endAt' => '2010',
                ],
                [
                    'school' => 'Lycée Bahuet',
                    'place' => 'Brive-la-Gaillarde',
                    'title' => ' BTS Informatique de Gestion',
                    'description' => 'Option DA',
                    'startAt' => '2007',
                    'endAt' => '2009',
                ],
            ],
            'languages' => [
                [
                    'code' => 'fr',
                    'level' => 'native',
                ],
                [
                    'code' => 'en',
                    'level' => 'professional',
                ],
            ],
            'other' => ['oss', 'afup'],
        ]);
    }
}
