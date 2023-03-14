<?php

declare(strict_types=1);

namespace App\Ui\Controller\Admin\Project;

use App\Domain\Project\Repository\ProjectRepository;
use App\Ui\Controller\AbstractController;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(
    path: '/project',
    name: 'project_index',
    methods: [Request::METHOD_GET]
)]
final class IndexController extends AbstractController
{
    public function __invoke(Request $request, ProjectRepository $repository, PaginatorInterface $paginator): Response
    {
        return $this->render('admin/project/index.html.twig', [
            'projects' => $paginator->paginate(
                $repository->queryAll(),
                $request->query->getInt('page', 1),
                $request->query->getInt('limit', 10),
            ),
        ]);
    }
}
