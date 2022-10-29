<?php

declare(strict_types=1);

namespace App\Http\Controller\Website\Project;

use App\Domain\Project\Enum\ProjectType;
use App\Domain\Project\Repository\ProjectRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(
    path: [
        'en' => '/projects/{page}',
        'fr' => '/projets/{page}',
    ],
    name: 'project_index',
    requirements: ['page' => '\d+'],
    methods: [Request::METHOD_GET]
)]
final class IndexController extends AbstractController
{
    public function __invoke(Request $request, ProjectRepository $repository, PaginatorInterface $paginator, int $page = 1): Response
    {
        $query = $repository->createQueryBuilder('p')
            ->where('p.type = :type')
            ->setParameter('type', ProjectType::Customer)
            ->getQuery()
        ;

        return $this->render('website/project/index.html.twig', [
            'projects' => $paginator->paginate($query, $page, 5),
        ]);
    }
}
