<?php

namespace App\Charts;

use App\Support\Livewire\ChartComponentData;
use ConsoleTVs\Charts\Classes\Chartjs\Chart;

class WaterLevelChart extends Chart
{
    /**
     * Initializes the chart.
     *
     * @return void
     */
    public function __construct(ChartComponentData $data)
    {
        parent::__construct();

        $this->loader(false);

        $this->options([
            'maintainAspectRatio' => false,
            'legend' => [
                'display' => false,
            ],
            'scales' => [
                'yAxes' => [
                    [
                        'ticks' => [
                            'maxTicksLimit' => 6,
                            'beginAtZero'   => true,
                        ],
                    ],
                ],
                'xAxes' => [
                    [
                        'display' => false,
                    ],
                ],
            ],
        ]);

        $this->labels($data->labels());

        $this->dataset("Upload speed (Mbps)", "line", $data->datasets()[0])->options([
            'backgroundColor'           => 'rgb(127,156,245, 0.4)',
            'borderColor'               => '#7F9CF5',
            'pointBackgroundColor'      => 'rgb(255, 255, 255, 0)',
            'pointBorderColor'          => 'rgb(255, 255, 255, 0)',
            'pointHoverBackgroundColor' => '#7F9CF5',
            'pointHoverBorderColor'     => '#7F9CF5',
            'borderWidth'               => 1,
            'pointRadius'               => 1,
        ]);
    }
}
