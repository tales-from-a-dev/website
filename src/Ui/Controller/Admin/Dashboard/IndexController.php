<?php

declare(strict_types=1);

namespace App\Ui\Controller\Admin\Dashboard;

use App\Domain\Blog\Repository\PostRepository;
use App\Domain\Project\Repository\ProjectRepository;
use App\Domain\Statistics\Helper\StatisticsHelper;
use App\Ui\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

#[Route(
    path: '/',
    name: 'dashboard_index',
    methods: [Request::METHOD_GET]
)]
final class IndexController extends AbstractController
{
    public function __construct(
        private readonly PostRepository $postRepository,
        private readonly ProjectRepository $projectRepository,
        private readonly CacheInterface $cache,
        private readonly ChartBuilderInterface $chartBuilder,
    ) {
    }

    public function __invoke(): Response
    {
        return $this->render('admin/dashboard/index.html.twig', [
            'posts' => $this->postRepository->findLatest(),
            'projects' => $this->projectRepository->findLatest(),
            'post_chart' => $this->buildPostChart(),
            'project_chart' => $this->buildProjectChart(),
        ]);
    }

    private function buildPostChart(): Chart
    {
        $lastYear = date('Y', strtotime('-1 year'));

        [$lastYearData, $currentYearData] = $this->cache->get('post_statistics', function (ItemInterface $item) use ($lastYear) {
            $item->expiresAfter(60 * 60);

            return [
                StatisticsHelper::formatCountByMonth($this->postRepository->countByMonth($lastYear)),
                StatisticsHelper::formatCountByMonth($this->postRepository->countByMonth()),
            ];
        });

        return $this->buildChart(
            labels: array_keys($lastYearData),
            datasets: [
                [
                    'label' => $lastYear,
                    'borderColor' => '#36a2eb',
                    'data' => array_values($lastYearData),
                    'tension' => 0.1,
                ],
                [
                    'label' => date('Y'),
                    'borderColor' => '#ff6384',
                    'data' => array_values($currentYearData),
                    'tension' => 0.1,
                ],
            ]
        );
    }

    private function buildProjectChart(): Chart
    {
        $lastYear = date('Y', strtotime('-1 year'));

        [$lastYearData, $currentYearData] = $this->cache->get('project_statistics', function (ItemInterface $item) use ($lastYear) {
            $item->expiresAfter(60 * 60);

            return [
                StatisticsHelper::formatCountByMonth($this->projectRepository->countByMonth($lastYear)),
                StatisticsHelper::formatCountByMonth($this->projectRepository->countByMonth()),
            ];
        });

        return $this->buildChart(
            labels: array_keys($lastYearData),
            datasets: [
                [
                    'label' => $lastYear,
                    'borderColor' => '#36a2eb',
                    'data' => array_values($lastYearData),
                    'tension' => 0.1,
                ],
                [
                    'label' => date('Y'),
                    'borderColor' => '#ff6384',
                    'data' => array_values($currentYearData),
                    'tension' => 0.1,
                ],
            ]
        );
    }

    /**
     * @param array<string> $labels
     * @param array<mixed>  $datasets
     */
    private function buildChart(array $labels, array $datasets): Chart
    {
        $chart = $this->chartBuilder->createChart(Chart::TYPE_LINE);
        $chart
            ->setData([
                'labels' => $labels,
                'datasets' => $datasets,
            ])
            ->setOptions([
                'scales' => [
                    'y' => [
                        'suggestedMin' => 0,
                        'suggestedMax' => 200,
                    ],
                ],
            ]);

        return $chart;
    }
}
