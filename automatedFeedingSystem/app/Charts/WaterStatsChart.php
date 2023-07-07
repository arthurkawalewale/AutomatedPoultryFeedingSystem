<?php

namespace App\Charts;

use App\Support\ChartComponentData;
use ConsoleTVs\Charts\Classes\Chartjs\Chart;

class WaterStatsChart extends Chart
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
                    'max' => 20,
                    'min' => 0,
                    'ticks' => [
                        'stepSize' => 4,
                    ],
                    'title' => [
                        'display' => true,
                        'align' => 'center',
                        'text' => 'Average Water volume',
                    ],
                ],
                'percentage' => [
                    'position' => 'right',
                    'max' => 20,
                    'min' => 0,
                    'ticks' => [
                        'stepSize' => 4,
                    ],
                    'title' => [
                        'display' => true,
                        'align' => 'center',
                        'text' => 'No. of Chicken',
                    ],
                ],
                'x' => [
                    'display' => true,
                    'title' => [
                        'display' => true,
                        'align' => 'center',
                        'text' => 'Time',
                    ],
                ],
            ],
        ]);

        $this->labels($data->labels());

        $this->dataset("Reservoir (cm)", "bar", $data->datasets()[0])->options([
            'backgroundColor'           => 'rgb(17, 122, 45, 0.4)',
            'fill'                      => true,
            'borderColor'               => 'rgb(17, 122, 45, 0.4)',
            'tooltip'                   => true,
            'yAxisID'                   => 'y'
        ]);

        $this->dataset("Trough (cm)", "bar", $data->datasets()[1])->options([
            'backgroundColor'           => 'rgb(127,156,245, 0.7)',
            'fill'                      => true,
            'borderColor'               => '#A3BFFA',
            'pointBackgroundColor'      => 'rgb(255, 255, 255, 0)',
            'pointBorderColor'          => 'rgb(255, 255, 255, 0)',
            'pointHoverBackgroundColor' => '#A3BFFA',
            'pointHoverBorderColor'     => '#A3BFFA',
            'borderWidth'               => 1,
            'pointRadius'               => 1,
            'tooltip'                   => true,
            'yAxisID'                   => 'y'
        ]);

        $this->dataset('No. of Chicken', 'line', $data->datasets()[2])->options([
            'backgroundColor'           => 'rgb(217, 146, 15, 0.7)',
            'borderColor'               => 'rgb(217, 146, 15, 0.7)',
            'pointBackgroundColor'      => 'rgb(217, 146, 15)',
            'pointBorderColor'          => 'rgb(217, 146, 15)',
            'pointHoverBorderWidth'     => 1,
            'pointHoverRadius'          => 3,
            'pointRadius'               => 2,
            'borderWidth'               => 2,
            'pointBorderWidth'          => 1,
            'pointHitRadius'            => 2,
            'pointStyle'                => 'circle',
            'tooltip'                   => true,
            'tension'                   =>  0.1,
            'yAxisID'                   => 'percentage',
            'showLine'                  => true,
        ]);
    }
}
