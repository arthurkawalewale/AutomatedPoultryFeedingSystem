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
        $water_level_data_sets = DB::table(DB::raw("(SELECT 'Sunday' AS day_of_week UNION ALL SELECT 'Monday' UNION ALL SELECT 'Tuesday' UNION ALL SELECT 'Wednesday' UNION ALL SELECT 'Thursday' UNION ALL SELECT 'Friday' UNION ALL SELECT 'Saturday') AS days_of_week"))
            ->leftJoin(DB::raw("(SELECT DAYNAME(created_at) AS day_of_week, AVG(reservoir_reading) AS avg_reservoir, AVG(trough_reading) AS avg_trough FROM water_readings WHERE created_at BETWEEN '{$startDate}' AND '{$endDate}' GROUP BY day_of_week) AS averages"), 'days_of_week.day_of_week', '=', 'averages.day_of_week')
            ->select('days_of_week.day_of_week', DB::raw('COALESCE(averages.avg_reservoir, 0) AS avg_reservoir'), DB::raw('COALESCE(averages.avg_trough, 0) AS avg_trough'))
            ->get();

        // Convert the stdClass objects to WaterReading models
        $water_level_data_sets = $water_level_data_sets->map(function ($item) {
            $waterReading = new WaterReading();
            $waterReading->day_of_week = $item->day_of_week;
            $waterReading->avg_reservoir = $item->avg_reservoir;
            $waterReading->avg_trough = $item->avg_trough;
            return $waterReading;
        });

        $labels = $water_level_data_sets->map(function(WaterReading $water_level_data_sets, $key) {
            return $water_level_data_sets->day_of_week;
        });

        $datasets = new Collection([
            $water_level_data_sets->map(function(WaterReading $water_level_data_sets) {
                return number_format($water_level_data_sets->avg_reservoir, 2, '.', '');
            }),

            $water_level_data_sets->map(function(WaterReading $water_level_data_sets) {
                return number_format($water_level_data_sets->avg_trough, 2, '.', '');
            }),
        ]);

        return (new ChartComponentData($labels, $datasets));
    }
}
