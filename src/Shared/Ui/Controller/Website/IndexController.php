<?php

declare(strict_types=1);

namespace App\Shared\Ui\Controller\Website;

use App\Experience\Infrastructure\Repository\ExperienceRepository;
use App\Settings\Infrastructure\Repository\SettingsRepository;
use App\Shared\Infrastructure\Seo\HomepageStructuredDataBuilder;
use App\Shared\Ui\Controller\AbstractController;
use Doctrine\Common\Collections\Order;
use Presta\SitemapBundle\Sitemap\Url\UrlConcrete;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(
    path: '/',
    name: 'index',
    options: [
        'sitemap' => [
            'priority' => 1.0,
            'changefreq' => UrlConcrete::CHANGEFREQ_WEEKLY,
            'section' => 'homepage',
        ],
    ],
    methods: [
        Request::METHOD_GET,
    ]
)]
final class IndexController extends AbstractController
{
    public function __invoke(
        ExperienceRepository $experienceRepository,
        SettingsRepository $settingsRepository,
        HomepageStructuredDataBuilder $structuredDataBuilder,
    ): Response {
        return $this->render('app/website/shared/index.html.twig', [
            'experiences' => $experienceRepository->findBy([], ['endAt' => Order::Descending->value]),
            'settings' => $settingsRepository->findFirst(),
            'structured_data' => $structuredDataBuilder->build(),
        ]);
    }
}
