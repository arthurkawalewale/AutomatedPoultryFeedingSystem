<?php

namespace App\Http\Livewire\dashboard;

use App\Charts\WaterStatsChart;
use App\Models\WaterReading;
use App\Support\ChartComponent;
use App\Support\ChartComponentData;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
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
        $water_level_data_sets = WaterReading::select(DB::raw('DAYNAME(created_at) AS day'), DB::raw('AVG(reservoir_reading) AS res_value, AVG(trough_reading) AS trough' ))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('day')
            ->orderBy('created_at')
            ->get();

        $labels = $water_level_data_sets->map(function(WaterReading $water_level_data_sets, $key) {
            return $water_level_data_sets->day;
        });

        $datasets = new Collection([
            $water_level_data_sets->map(function(WaterReading $water_level_data_sets) {
                return number_format($water_level_data_sets->res_value, 2, '.', '');
            }),

            $water_level_data_sets->map(function(WaterReading $water_level_data_sets) {
                return number_format($water_level_data_sets->trough, 2, '.', '');
            }),
        ]);

        $labels = collect([
            'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'
        ]);

        return (new ChartComponentData($labels, $datasets));
    }
}
