<?php

declare(strict_types=1);

namespace App\Ui\Controller\Website\Project;

use App\Domain\Project\Entity\Project;
use App\Ui\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(
    path: [
        'en' => '/projects/{slug}',
        'fr' => '/projets/{slug}',
    ],
    name: 'project_show',
    requirements: ['slug' => '[\w-]+'],
    methods: [Request::METHOD_GET]
)]
final class ShowController extends AbstractController
{
    public function __invoke(Project $project): Response
    {
        return $this->render('website/project/show.html.twig', [
            'project' => $project,
        ]);
    }
}
