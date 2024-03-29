<?php

namespace App\Charts;

use App\Support\ChartComponentData;
use ConsoleTVs\Charts\Classes\Chartjs\Chart;

class WaterLevelChart extends Chart
{
    /**
     * Initializes the live monitored water chart.
     * This chart displays real time data from the sensors in the trough and tanks/reservoirs.
     * It renders new data every 1s. It uses Laravel livewire for component update without having to refresh the whole page.
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
                        'text' => 'water volume',
                    ],
                ],
                'x' => [
                    'display' => true,
                    'title' => [
                        'display' => true,
                        'align' => 'center',
                        'text' => 'Time recorded',
                    ],
                ],
            ],
        ]);

        $this->labels($data->labels());

        $this->dataset("Reservoir (cm)", "line", $data->datasets()[0])->options([
            'backgroundColor'           => 'rgb(17, 122, 45, 0.4)',
            'fill'                      => true,
            'borderColor'               => 'rgb(17, 122, 45, 0.4)',
            'pointBackgroundColor'      => 'rgb(255, 255, 255, 0)',
            'pointBorderColor'          => 'rgb(255, 255, 255, 0)',
            'pointHoverBackgroundColor' => '#117a2d',
            'pointHoverBorderColor'     => 'black',
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
            'pointHoverBackgroundColor' => '#A3BFFA',
            'pointHoverBorderColor'     => '#A3BFFA',
            'borderWidth'               => 1,
            'pointRadius'               => 1,
            'tooltip'                   => true,
            'tension'                   =>  0.4
        ]);
    }
}
