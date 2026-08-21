<?php

declare(strict_types=1);

namespace App\Blog\Ui\Controller\Website;

use App\Blog\Domain\Enum\BlogCategoryEnum;
use App\Blog\Infrastructure\Repository\BlogPostRepository;
use App\Blog\Infrastructure\Seo\BlogCategoryAlternateResolver;
use App\Shared\Ui\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

#[Route(
    path: '/category/{category}',
    name: 'category',
    requirements: [
        'category' => Requirement::ASCII_SLUG,
    ],
    methods: [
        Request::METHOD_GET,
    ]
)]
final class CategoryController extends AbstractController
{
    public function __construct(
        private readonly BlogPostRepository $blogPostRepository,
        private readonly BlogCategoryAlternateResolver $blogCategoryAlternateResolver,
    ) {
    }

    public function __invoke(Request $request, BlogCategoryEnum $category): Response
    {
        $posts = $this->blogPostRepository->findByCategory($request->getLocale(), $category);
        if ([] === $posts) {
            throw $this->createNotFoundException(\sprintf('No blog post found for category "%s".', $category->value));
        }

        return $this->render('app/website/blog/category.html.twig', [
            'category' => $category,
            'posts' => $posts,
            'alternates' => $this->blogCategoryAlternateResolver->resolve($category),
        ]);
    }
}
