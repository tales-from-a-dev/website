<?php

declare(strict_types=1);

namespace App\Ui\Controller\Admin\Post;

use App\Core\Enum\AlertStatus;
use App\Domain\Blog\Entity\Post;
use App\Domain\Blog\Repository\PostRepository;
use App\Ui\Controller\AbstractController;
use App\Ui\Form\PostType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(
    path: '/post/{id}/edit',
    name: 'post_edit',
    methods: [Request::METHOD_GET, Request::METHOD_POST]
)]
final class EditController extends AbstractController
{
    public function __invoke(Request $request, Post $post, PostRepository $repository): Response
    {
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $repository->save($post, true);

            $this->addToast(AlertStatus::Success, 'edit.success', [
                'selector' => $post->getEntityName(),
                'name' => $post,
            ]);
        }

        return $this->render('admin/post/edit.html.twig', [
            'post' => $post,
            'form' => $form,
        ]);
    }
}
