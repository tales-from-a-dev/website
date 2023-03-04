<?php

declare(strict_types=1);

namespace App\Ui\Controller\Admin\Dashboard;

use App\Domain\Blog\Repository\PostRepository;
use App\Domain\Project\Repository\ProjectRepository;
use App\Ui\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(
    path: '/',
    name: 'dashboard_index',
    methods: [Request::METHOD_GET]
)]
final class IndexController extends AbstractController
{
    public function __invoke(
        PostRepository $postRepository,
        ProjectRepository $projectRepository,
    ): Response {
        return $this->render('admin/dashboard/index.html.twig', [
            'posts' => $postRepository->findLatest(),
            'projects' => $projectRepository->findLatest(),
        ]);
    }
}
