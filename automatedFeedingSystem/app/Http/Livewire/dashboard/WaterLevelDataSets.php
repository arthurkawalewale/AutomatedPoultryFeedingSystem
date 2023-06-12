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
        //$water_level_data_sets = WaterReading::query()->latest('id')->orderBy('id','desc')->take(5)->get();

        //dd($water_level_data_sets);

        $startOfDay = Carbon::today()->startOfDay();
        $endOfDay = Carbon::today()->endOfDay();

        $water_level_data_sets = WaterReading::query()
            ->whereBetween('created_at', [$startOfDay, $endOfDay])
            ->orderBy('id', 'desc')
            ->latest('id')
            ->take(5)
            ->get();

        //dd($water_level_data_sets);

        $labels = $water_level_data_sets->map(function(WaterReading $water_level_data_sets, $key) {
            return $water_level_data_sets->created_at->format('H:i');
        });

        //dd($labels);

        $labels = $labels->reverse()->values();

        $datasets = new Collection([
            $water_level_data_sets->map(function(WaterReading $water_level_data_sets) {
                return number_format($water_level_data_sets->reservoir_reading, 2, '.', '');
            })->reverse()->values(),

            $water_level_data_sets->map(function(WaterReading $water_level_data_sets) {
                return number_format($water_level_data_sets->trough_reading, 2, '.', '');
            })->reverse()->values(),
        ]);

        //dd($datasets);

        return (new ChartComponentData($labels, $datasets));
    }
}
