<?php

declare(strict_types=1);

namespace App\Shared\Ui\Controller\Dashboard;

use App\Analytics\Infrastructure\State\Provider\VisitsPerMonthProvider;
use App\Shared\Ui\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

#[Route(
    path: '/',
    name: 'index',
    methods: [
        Request::METHOD_GET,
    ]
)]
class IndexController extends AbstractController
{
    public function __construct(
        private readonly VisitsPerMonthProvider $visitsPerMonthProvider,
        private readonly ChartBuilderInterface $chartBuilder,
    ) {
    }

    public function __invoke(): Response
    {
        $dataset = $this->visitsPerMonthProvider->provide();

        $monthlyChart = $this->chartBuilder->createChart(Chart::TYPE_LINE);
        $monthlyChart
            ->setData([
                'labels' => array_values($dataset->labels),
                'datasets' => [
                    [
                        'data' => $dataset->data,
                    ],
                ],
            ])
            ->setOptions([
                'plugins' => [
                    'legend' => [
                        'display' => false,
                    ],
                ],
                'scales' => [
                    'y' => [
                        'display' => false,
                    ],
                ],
            ])
        ;

        return $this->render('app/dashboard/index.html.twig', [
            'monthly_chart' => $monthlyChart,
        ]);
    }
}
