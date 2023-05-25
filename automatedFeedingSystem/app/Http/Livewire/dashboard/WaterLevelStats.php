<?php

namespace App\Http\Livewire\dashboard;

use App\Charts\WaterStatsChart;
use App\Models\WaterReading;
use App\Support\ChartComponent;
use App\Support\ChartComponentData;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Livewire\Component;

class WaterLevelStats extends ChartComponent
{
    /**
     * @return string
     */
    protected function view(): string
    {
        return 'livewire.water-level-stats';
    }

    /**
     * @return string
     */
    protected function chartClass(): string
    {
        return WaterStatsChart::class;
    }

    /**
     * @return \App\Support\ChartComponentData
     */
    protected function chartData(): ChartComponentData
    {
        // Get the start and end dates for the desired week
        $startDate = Carbon::now()->startOfWeek();
        $endDate = Carbon::now()->endOfWeek();

        // Retrieve records for the specified week
        $water_level_data_sets = WaterReading::whereBetween('created_at', [$startDate, $endDate])->take(7)->get();

        //dd($water_level_data_sets);

        $labels = $water_level_data_sets->map(function(WaterReading $water_level_data_sets, $key) {
            return $water_level_data_sets->created_at->format('D');
        });

        dd($labels);

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
