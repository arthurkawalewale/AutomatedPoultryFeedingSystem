<?php

namespace App\Charts;

use App\Support\ChartComponentData;
use ConsoleTVs\Charts\Classes\Chartjs\Chart;

class FeedStatsChart extends Chart
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
            'scales' => [
                'y' => [
                    'beginAtZero'   => true,
                    'max' => 16,
                    'min' => 0,
                    'ticks' => [
                        'stepSize' => 4,
                    ],
                    'title' => [
                        'display' => true,
                        'align' => 'center',
                        'text' => 'Feed volume',
                    ],
                ],
                'x' => [
                    'display' => true,
                    'title' => [
                        'display' => true,
                        'align' => 'center',
                        'text' => 'Time Recorded',
                    ],
                ],
            ],
        ]);

        $this->labels($data->labels());

        $this->dataset("Reservoir (cm)", "bar", $data->datasets()[0])->options([
            'backgroundColor'           => 'rgb(77, 77, 82, 0.4)',
            'fill'                      => true,
            'borderColor'               => 'rgb(77, 77, 82, 0.4)',
            'pointBackgroundColor'      => 'rgb(255, 255, 255, 0)',
            'pointBorderColor'          => 'rgb(255, 255, 255, 0)',
            'pointHoverBackgroundColor' => '#7F9CF5',
            'pointHoverBorderColor'     => '#7F9CF5',
            'borderWidth'               => 1,
            'pointRadius'               => 1,
            'tooltip'                   => true,
            'tension'                   =>  0.4
        ]);

        $this->dataset("Trough (cm)", "bar", $data->datasets()[1])->options([
            'backgroundColor'           => 'rgb(199, 12, 40, 0.7)',
            'fill'                      => true,
            'borderColor'               => 'rgb(199, 12, 40, 0.7)',
            'pointBackgroundColor'      => 'rgb(255, 255, 255, 0)',
            'pointBorderColor'          => 'rgb(255, 255, 255, 0)',
            'pointHoverBackgroundColor' => '#7F9CF5',
            'pointHoverBorderColor'     => '#7F9CF5',
            'borderWidth'               => 1,
            'pointRadius'               => 1,
            'tooltip'                   => true,
            'tension'                   =>  0.4
        ]);
    }
}
