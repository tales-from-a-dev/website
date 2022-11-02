<?php

declare(strict_types=1);

namespace App\Http\Controller\Website\Blog;

use App\Domain\Blog\Repository\PostRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(
    path: '/blog/{page}',
    name: 'blog_index',
    requirements: ['page' => '\d+'],
    methods: [Request::METHOD_GET]
)]
final class IndexController extends AbstractController
{
    public function __invoke(Request $request, PostRepository $repository, PaginatorInterface $paginator, int $page = 1): Response
    {
        return $this->render('website/blog/index.html.twig', [
            'posts' => $paginator->paginate($repository->queryAllPublished(), $page, 5),
        ]);
    }
}
