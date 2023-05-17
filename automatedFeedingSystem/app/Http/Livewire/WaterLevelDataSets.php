<?php

namespace App\Http\Livewire;

use App\Charts\WaterLevelChart;
use App\Support\Livewire\ChartComponent;
use App\Support\Livewire\ChartComponentData;
use Illuminate\Support\Collection;
use Livewire\Component;

class WaterLevelDataSets extends ChartComponent
{
    /*public function render()
    {
        return view('livewire.water-level-data-sets');
    }*/


    /**
     * @return string
     */
    protected function view(): string
    {
        return 'livewire.water-level-data-sets';
    }

    /**
     * @return string
     */
    protected function chartClass(): string
    {
        return WaterLevelChart::class;
    }

    /**
     * @return \App\Support\Livewire\ChartComponentData
     */
    protected function chartData(): ChartComponentData
    {
        $water_level_data_sets = WanSpeedTest::query()
            ->select(['id', 'created_at', 'speed_down_mbps', 'speed_up_mbps', 'ping_ms'])
            ->where('created_at', '>=', Carbon::now()->subDay())
            ->where('created_at', '<=', Carbon::now())
            ->get();

        $labels = $water_level_data_sets->map(function(WanSpeedTest $water_level_data_sets, $key) {
            return $water_level_data_sets->created_at->format('Y-m-d H:i:s');
        });

        $datasets = new Collection([
            $$water_level_data_sets->map(function(WanSpeedTest $water_level_data_sets) {
                return number_format($water_level_data_sets->speed_up_mbps, 2, '.', '');
            }),
        ]);

        return (new ChartComponentData($labels, $datasets));
    }
}
