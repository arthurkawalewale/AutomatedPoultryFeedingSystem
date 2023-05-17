<?php

namespace App\Http\Livewire\dashboard;

use App\Charts\WaterLevelChart;
use App\Models\WaterReading;
use App\Support\ChartComponent;
use App\Support\ChartComponentData;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class WaterLevelDataSets extends ChartComponent
{
    /*public function render()
    {
        return view('livewire.water-level-data-sets');
    }*


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
     * @return \App\Support\ChartComponentData
     */
    protected function chartData(): ChartComponentData
    {
        $water_level_data_sets = WaterReading::query()
            ->select(['id', 'created_at', 'reading'])
            ->where('created_at', '>=', Carbon::now()->subDays(4))
            ->where('created_at', '<=', Carbon::now())
            ->get();
        //$water_level_data_sets = WaterReading::query()->latest();

        $labels = $water_level_data_sets->map(function(WaterReading $water_level_data_sets, $key) {
            return $water_level_data_sets->created_at->format('Y-m-d H:i:s');
        });

        $datasets = new Collection([
            $water_level_data_sets->map(function(WaterReading $water_level_data_sets) {
                return number_format($water_level_data_sets->reading, 2, '.', '');
            }),
        ]);

        return (new ChartComponentData($labels, $datasets));
    }
}
