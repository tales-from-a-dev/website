<?php

declare(strict_types=1);

namespace App\Blog\Ui\Controller\Website;

use App\Blog\Infrastructure\Repository\BlogPostRepository;
use App\Shared\Ui\Controller\AbstractController;
use Presta\SitemapBundle\Sitemap\Url\UrlConcrete;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(
    path: '/',
    name: 'index',
    options: [
        'sitemap' => [
            'priority' => 0.8,
            'changefreq' => UrlConcrete::CHANGEFREQ_WEEKLY,
            'section' => 'blog',
        ],
    ],
    methods: [
        Request::METHOD_GET,
    ]
)]
final class IndexController extends AbstractController
{
    public function __construct(
        private readonly BlogPostRepository $blogPostRepository,
    ) {
    }

    public function __invoke(Request $request): Response
    {
        return $this->render('app/website/blog/index.html.twig', [
            'posts' => $this->blogPostRepository->findAll($request->getLocale()),
        ]);
    }
}
