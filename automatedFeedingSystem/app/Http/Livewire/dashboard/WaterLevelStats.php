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
    public $interval = "weekly";
    public $intervals = array("weekly"=>'This Week', "monthly"=>'Monthly', "yearly"=>'Yearly');

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
        $interval = $this->interval;

        // Get the start and end dates for the desired week
        $startDate = Carbon::now()->startOfWeek();
        $endDate = Carbon::now()->endOfWeek();

        $currentYear = date('Y');
        $startingYear = $currentYear - 5;


        $water_level_data_sets = collect();

        if ($interval === 'weekly') {
            $water_level_data_sets = DB::table(DB::raw("(SELECT 'Sunday' AS day_of_week UNION ALL SELECT 'Monday' UNION ALL SELECT 'Tuesday' UNION ALL SELECT 'Wednesday' UNION ALL SELECT 'Thursday' UNION ALL SELECT 'Friday' UNION ALL SELECT 'Saturday') AS days_of_week"))
                ->leftJoin(DB::raw("(SELECT DAYNAME(created_at) AS day_of_week, AVG(reservoir_reading) AS avg_reservoir, AVG(trough_reading) AS avg_trough, AVG(number_of_birds) as number_of_birds FROM water_readings WHERE created_at BETWEEN '{$startDate}' AND '{$endDate}' GROUP BY day_of_week) AS averages"), 'days_of_week.day_of_week', '=', 'averages.day_of_week')
                ->select('days_of_week.day_of_week', DB::raw('COALESCE(averages.avg_reservoir, 0) AS avg_reservoir'), DB::raw('COALESCE(averages.avg_trough, 0) AS avg_trough'), DB::raw('COALESCE(averages.number_of_birds, 0) AS number_of_birds'))
                ->get();
        }elseif ($interval === 'monthly') {
            $water_level_data_sets = DB::table(DB::raw("(SELECT 'January' AS month UNION ALL SELECT 'February' UNION ALL SELECT 'March' UNION ALL SELECT 'April' UNION ALL SELECT 'May' UNION ALL SELECT 'June' UNION ALL SELECT 'July' UNION ALL SELECT 'August' UNION ALL SELECT 'September' UNION ALL SELECT 'October' UNION ALL SELECT 'November' UNION ALL SELECT 'December') AS months"))
                ->leftJoin(DB::raw("(SELECT MONTHNAME(created_at) AS month, AVG(reservoir_reading) AS avg_reservoir, AVG(trough_reading) AS avg_trough, MAX(number_of_birds) as number_of_birds FROM water_readings WHERE YEAR(created_at) = 2023 GROUP BY month) AS averages"), 'months.month', '=', 'averages.month')
                ->select('months.month', DB::raw('COALESCE(averages.avg_reservoir, 0) AS avg_reservoir'), DB::raw('COALESCE(averages.avg_trough, 0) AS avg_trough'), DB::raw('COALESCE(averages.number_of_birds, 0) AS number_of_birds'))
                ->get();
        }elseif ($interval === 'yearly') {
            $water_level_data_sets = DB::table('water_readings')
                ->select(DB::raw('YEAR(created_at) AS year'), DB::raw('AVG(reservoir_reading) AS avg_reservoir'), DB::raw('AVG(trough_reading) AS avg_trough'), DB::raw('MAX(number_of_birds) AS number_of_birds'))
                ->whereBetween(DB::raw('YEAR(created_at)'), [$startingYear, $currentYear])
                ->groupBy(DB::raw('YEAR(created_at)'))
                ->get();
        }

        // Convert the stdClass objects to WaterReading models
        $water_level_data_sets = $water_level_data_sets->map(function ($item) use ($interval) {
            $waterReading = new WaterReading();
            $waterReading->time = $interval === 'weekly' ? $item->day_of_week : ($interval === 'monthly' ? $item->month : $item->year);
            $waterReading->avg_reservoir = $item->avg_reservoir;
            $waterReading->avg_trough = $item->avg_trough;
            $waterReading->number_of_birds = $item->number_of_birds;
            return $waterReading;
        });

        $labels = $water_level_data_sets->map(function(WaterReading $water_level_data_sets, $key) {
            return $water_level_data_sets->time;
        });

        $datasets = new Collection([
            $water_level_data_sets->map(function(WaterReading $water_level_data_sets) {
                return number_format($water_level_data_sets->avg_reservoir, 2, '.', '');
            }),

            $water_level_data_sets->map(function(WaterReading $water_level_data_sets) {
                return number_format($water_level_data_sets->avg_trough, 2, '.', '');
            }),

            $water_level_data_sets->map(function(WaterReading $water_level_data_sets) {
                return number_format($water_level_data_sets->number_of_birds, 0, '.', '');
            }),
        ]);

        return (new ChartComponentData($labels, $datasets));
    }
}
