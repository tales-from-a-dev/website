<?php

declare(strict_types=1);

namespace App\Shared\Ui\Controller\Dashboard;

use App\Analytics\Infrastructure\State\Provider\VisitsPerDayProvider;
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
        private readonly VisitsPerDayProvider $visitsPerDayProvider,
        private readonly ChartBuilderInterface $chartBuilder,
    ) {
    }

    public function __invoke(): Response
    {
        return $this->render('app/dashboard/index.html.twig', [
            'monthly_chart' => $this->buildMonthlyChart(),
            'daily_chart' => $this->buildDailyChart(),
        ]);
    }

    private function buildMonthlyChart(): Chart
    {
        $monthlyChartDataset = $this->visitsPerMonthProvider->provide();

        $monthlyChart = $this->chartBuilder->createChart(Chart::TYPE_LINE);
        $monthlyChart
            ->setData([
                'labels' => $monthlyChartDataset->labels,
                'datasets' => [
                    [
                        'data' => $monthlyChartDataset->data,
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

        return $monthlyChart;
    }

    private function buildDailyChart(): Chart
    {
        $dailyChartDataset = $this->visitsPerDayProvider->provide();

        $dailyChart = $this->chartBuilder->createChart(Chart::TYPE_LINE);
        $dailyChart
            ->setData([
                'labels' => $dailyChartDataset->labels,
                'datasets' => [
                    [
                        'data' => $dailyChartDataset->data,
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

        return $dailyChart;
    }
}
