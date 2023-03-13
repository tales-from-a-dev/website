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
use Symfony\Component\Routing\Annotation\Route;

#[Route(
    path: '/post/new',
    name: 'post_new',
    methods: [Request::METHOD_GET, Request::METHOD_POST]
)]
final class NewController extends AbstractController
{
    public function __invoke(Request $request, PostRepository $repository): Response
    {
        $post = new Post();

        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $repository->save($post, true);

            $this->addToast(AlertStatus::Success, 'new.success', [
                'selector' => $post->getEntityName(),
                'name' => $post,
            ]);

            return $this->redirectToRoute('app_admin_post_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/post/new.html.twig', [
            'post' => $post,
            'form' => $form,
        ]);
    }
}
