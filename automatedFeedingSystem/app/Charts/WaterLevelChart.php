<?php

namespace App\Charts;

use App\Support\ChartComponentData;
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
                'y' => [
                    'beginAtZero'   => true,
                    'max' => 16,
                    'min' => 0,
                    'ticks' => [
                        'stepSize' => 4,
                    ],
                ],
                'x' => [
                    'display' => true,
                ],
            ],
        ]);

        $this->labels($data->labels());

        $this->dataset("Tank (cm)", "line", $data->datasets()[0])->options([
            'backgroundColor'           => 'rgb(127,156,245, 0.4)',
            'fill'                      => true,
            'borderColor'               => '#7F9CF5',
            'pointBackgroundColor'      => 'rgb(255, 255, 255, 0)',
            'pointBorderColor'          => 'rgb(255, 255, 255, 0)',
            'pointHoverBackgroundColor' => '#7F9CF5',
            'pointHoverBorderColor'     => '#7F9CF5',
            'borderWidth'               => 1,
            'pointRadius'               => 1,
            'tooltip'                   => true,
            'tension'                   =>  0.4
        ]);

        $this->dataset("Trough (cm)", "line", $data->datasets()[1])->options([
            'backgroundColor'           => 'rgb(127,156,245, 0.7)',
            'fill'                      => true,
            'borderColor'               => '#A3BFFA',
            'pointBackgroundColor'      => 'rgb(255, 255, 255, 0)',
            'pointBorderColor'          => 'rgb(255, 255, 255, 0)',
            'pointHoverBackgroundColor' => '##A3BFFA',
            'pointHoverBorderColor'     => '##A3BFFA',
            'borderWidth'               => 1,
            'pointRadius'               => 1,
            'tooltip'                   => true,
            'tension'                   =>  0.4
        ]);
    }
}
