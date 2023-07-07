<?php

namespace App\Charts;

use App\Support\ChartComponentData;
use ConsoleTVs\Charts\Classes\Chartjs\Chart;

class FeedStatsChart extends Chart
{
    /**
     * Initializes the chart.
     * It shows stats for both the troughs and the water tanks/reservoirs.
     * The troughs and reservoirs have different maximum water levels.
     * The chart provide stats for Weekly, Monthly and Yearly time intervals.
     * It also has a linear chart that is displaying the number of birds (chickens).
     * This is done so that the information displayed on the water levels has some meaning when coupled up with the number chicken at that time.
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
                    'max' => 30,
                    'min' => 0,
                    'ticks' => [
                        'stepSize' => 5,
                    ],
                    'title' => [
                        'display' => true,
                        'align' => 'center',
                        'text' => 'Average Feed volume',
                    ],
                ],
                'percentage' => [
                    'position' => 'right',
                    'max' => 30,
                    'min' => 0,
                    'ticks' => [
                        'stepSize' => 5,
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
            'backgroundColor'           => 'rgb(77, 77, 82, 0.4)',
            'fill'                      => true,
            'borderColor'               => 'rgb(77, 77, 82, 0.4)',
            'borderWidth'               => 1,
            'tooltip'                   => true,
        ]);

        $this->dataset("Trough (cm)", "bar", $data->datasets()[1])->options([
            'backgroundColor'           => 'rgb(199, 12, 40, 0.7)',
            'fill'                      => true,
            'borderColor'               => 'rgb(199, 12, 40, 0.7)',
            'borderWidth'               => 1,
            'tooltip'                   => true,
        ]);

        $this->dataset('No. of Chicken', 'line', $data->datasets()[2])->options([
            'backgroundColor'           => 'rgb(17, 122, 45, 0.4)',
            'borderColor'               => 'rgb(17, 122, 45, 0.4)',
            'pointBackgroundColor'      => 'rgb(9, 189, 48)',
            'pointBorderColor'          => 'rgba(9, 189, 48)',
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
            'showLine'                  => true
        ]);
    }
}
