<?php

declare(strict_types=1);

namespace App\Analytics\Ui\Controller\Website;

use App\Shared\Ui\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\Cache;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[Route(
    path: '/robots.txt',
    name: 'robots',
    requirements: [
        '_format' => 'txt',
    ],
    methods: [
        Request::METHOD_GET,
    ],
    format: 'txt'
)]
#[Cache(
    maxage: 3600,
    smaxage: 86400,
    public: true,
    mustRevalidate: true,
)]
final class RobotController extends AbstractController
{
    public function __invoke(): Response
    {
        return $this->render('app/website/analytics/robots.txt.twig', [
            'sitemap' => $this->generateUrl('PrestaSitemapBundle_index', [
                '_format' => 'xml',
            ], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);
    }
}
