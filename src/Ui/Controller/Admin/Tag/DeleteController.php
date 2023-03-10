<?php

declare(strict_types=1);

namespace App\Ui\Controller\Admin\Tag;

use App\Core\Enum\AlertStatus;
use App\Domain\Blog\Entity\Tag;
use App\Domain\Blog\Repository\TagRepository;
use App\Ui\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(
    path: '/tag/{id}',
    name: 'tag_delete',
    methods: [Request::METHOD_DELETE]
)]
final class DeleteController extends AbstractController
{
    public function __invoke(Request $request, Tag $tag, TagRepository $repository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$tag->getId(), (string) $request->request->get('_token'))) {
            $repository->remove($tag, true);

            $this->addToast(AlertStatus::Success, 'delete.success', [
                'selector' => $tag->getEntityName(),
                'name' => $tag,
            ]);
        }

        return $this->redirectToRoute('app_admin_tag_index', [], Response::HTTP_SEE_OTHER);
    }
}
