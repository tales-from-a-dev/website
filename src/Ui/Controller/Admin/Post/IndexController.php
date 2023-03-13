<?php

declare(strict_types=1);

namespace App\Ui\Controller\Admin\Post;

use App\Domain\Blog\Repository\PostRepository;
use App\Ui\Controller\AbstractController;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(
    path: '/post',
    name: 'post_index',
    methods: [Request::METHOD_GET]
)]
final class IndexController extends AbstractController
{
    public function __invoke(Request $request, PostRepository $repository, PaginatorInterface $paginator): Response
    {
        return $this->render('admin/post/index.html.twig', [
            'posts' => $paginator->paginate(
                $repository->queryAll(),
                $request->query->getInt('page', 1),
                $request->query->getInt('limit', 10),
            ),
        ]);
    }
}
