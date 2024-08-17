<?php

declare(strict_types=1);

namespace App\Ui\Controller\Admin\Tag;

use App\Domain\Blog\Repository\TagRepository;
use App\Ui\Controller\AbstractController;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(
    path: '/tag',
    name: 'tag_index',
    methods: [Request::METHOD_GET]
)]
final class IndexController extends AbstractController
{
    public function __invoke(Request $request, TagRepository $repository, PaginatorInterface $paginator): Response
    {
        return $this->render('admin/tag/index.html.twig', [
            'tags' => $paginator->paginate(
                $repository->queryAll(),
                $request->query->getInt('page', 1),
                $request->query->getInt('limit', 10),
            ),
        ]);
    }
}
