<?php

declare(strict_types=1);

namespace App\Ui\Controller\Admin\Post;

use App\Core\Enum\AlertStatus;
use App\Domain\Blog\Entity\Post;
use App\Domain\Blog\Repository\PostRepository;
use App\Ui\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(
    path: '/post/{id}',
    name: 'post_delete',
    methods: [Request::METHOD_DELETE]
)]
final class DeleteController extends AbstractController
{
    public function __invoke(Request $request, Post $post, PostRepository $repository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$post->getId(), (string) $request->request->get('_token'))) {
            $repository->remove($post, true);

            $this->addToast(AlertStatus::Success, 'delete.success', [
                'selector' => $post->getEntityName(),
                'name' => $post,
            ]);
        }

        return $this->redirectToRoute('app_admin_tag_index', [], Response::HTTP_SEE_OTHER);
    }
}
