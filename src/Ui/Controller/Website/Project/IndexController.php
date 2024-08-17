<?php

declare(strict_types=1);

namespace App\Ui\Controller\Website\Project;

use App\Domain\Project\Enum\ProjectType;
use App\Domain\Project\Repository\ProjectRepository;
use App\Ui\Controller\AbstractController;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(
    path: [
        'en' => '/projects',
        'fr' => '/projets',
    ],
    name: 'project_index',
    methods: [Request::METHOD_GET]
)]
final class IndexController extends AbstractController
{
    public function __invoke(Request $request, ProjectRepository $repository, PaginatorInterface $paginator): Response
    {
        return $this->render('website/project/index.html.twig', [
            'projects' => $paginator->paginate(
                $repository->queryAllByType(ProjectType::Customer),
                $request->query->getInt('page', 1),
                5
            ),
        ]);
    }
}
