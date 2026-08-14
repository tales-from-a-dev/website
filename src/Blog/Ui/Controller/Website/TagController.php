<?php

declare(strict_types=1);

namespace App\Blog\Ui\Controller\Website;

use App\Blog\Infrastructure\Repository\BlogPostRepository;
use App\Shared\Ui\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

#[Route(
    path: '/tag/{tag}',
    name: 'tag',
    requirements: [
        'tag' => Requirement::ASCII_SLUG,
    ],
    methods: [
        Request::METHOD_GET,
    ]
)]
final class TagController extends AbstractController
{
    public function __construct(
        private readonly BlogPostRepository $blogPostRepository,
    ) {
    }

    public function __invoke(Request $request, string $tag): Response
    {
        $posts = $this->blogPostRepository->findByTag($request->getLocale(), $tag);
        if ([] === $posts) {
            throw $this->createNotFoundException(\sprintf('No blog post found for tag "%s".', $tag));
        }

        return $this->render('app/website/blog/tag.html.twig', [
            'tag' => $tag,
            'posts' => $posts,
        ]);
    }
}
