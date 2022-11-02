<?php

declare(strict_types=1);

namespace App\Http\Controller\Website\Blog;

use App\Core\Enum\Action;
use App\Domain\Blog\Entity\Post;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(
    path: '/blog/{slug}',
    name: 'blog_show',
    requirements: ['slug' => '[\w-]+'],
    methods: [Request::METHOD_GET]
)]
final class ShowController extends AbstractController
{
    public function __invoke(Request $request, Post $post): Response
    {
        $this->denyAccessUnlessGranted(Action::View->value, $post);

        return $this->render('website/blog/show.html.twig', [
            'post' => $post,
        ]);
    }
}
