<?php

declare(strict_types=1);

namespace App\Ui\Controller\Website\Blog;

use App\Domain\Blog\Entity\Tag;
use App\Ui\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(
    path: '/blog/tag/{slug}',
    name: 'blog_tag',
    requirements: ['slug' => '[\w-]+'],
    methods: [Request::METHOD_GET]
)]
final class TagController extends AbstractController
{
    public function __invoke(Tag $tag): Response
    {
        return $this->render('website/blog/tag.html.twig', [
            'tag' => $tag,
        ]);
    }
}
