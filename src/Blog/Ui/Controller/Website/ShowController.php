<?php

declare(strict_types=1);

namespace App\Blog\Ui\Controller\Website;

use App\Blog\Infrastructure\Repository\BlogPostRepository;
use App\Blog\Infrastructure\Seo\BlogPostAlternateResolver;
use App\Blog\Infrastructure\Seo\BlogPostStructuredDataBuilder;
use App\Shared\Ui\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(
    path: '/{slug}',
    name: 'show',
    requirements: [
        'slug' => '[a-z0-9-]+',
    ],
    methods: [
        Request::METHOD_GET,
    ]
)]
final class ShowController extends AbstractController
{
    public function __construct(
        private readonly BlogPostRepository $blogPostRepository,
        private readonly BlogPostAlternateResolver $blogPostAlternateResolver,
        private readonly BlogPostStructuredDataBuilder $structuredDataBuilder,
    ) {
    }

    public function __invoke(Request $request, string $slug): Response
    {
        $post = $this->blogPostRepository->findOneBySlug($request->getLocale(), $slug);
        if (null === $post) {
            throw $this->createNotFoundException(\sprintf('No blog post found for slug "%s".', $slug));
        }

        return $this->render('app/website/blog/show.html.twig', [
            'post' => $post,
            'content' => $this->blogPostRepository->findContent($post),
            'alternates' => $this->blogPostAlternateResolver->resolve($post),
            'structured_data' => $this->structuredDataBuilder->build($post),
        ]);
    }
}
