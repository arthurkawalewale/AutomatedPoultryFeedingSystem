<?php

namespace App\Http\Livewire\dashboard;

use App\Charts\FeedStatsChart;
use App\Models\WaterReading;
use App\Support\ChartComponentData;
use App\Support\ChartComponent;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class FeedLevelStats extends ChartComponent
{
    public $interval = "weekly";
    public $intervals = array("weekly"=>'This Week', "monthly"=>'Monthly', "yearly"=>'Yearly');

    /**
     * @return string
     */
    protected function view(): string
    {
        return 'livewire.feed-level-stats';
    }

    /**
     * @return string
     */
    protected function chartClass(): string
    {
        return FeedStatsChart::class;
    }

    /**
     * @return ChartComponentData
     */
    protected function chartData(): ChartComponentData
    {
        $interval = $this->interval;

        // Get the start and end dates for the desired week
        $startDate = Carbon::now()->startOfWeek();
        $endDate = Carbon::now()->endOfWeek();

        $currentYear = date('Y');
        $startingYear = $currentYear - 5;

        $feed_level_data_sets = collect();

        if ($interval === 'weekly') {
            $feed_level_data_sets = DB::table(DB::raw("(SELECT 'Sunday' AS day_of_week UNION ALL SELECT 'Monday' UNION ALL SELECT 'Tuesday' UNION ALL SELECT 'Wednesday' UNION ALL SELECT 'Thursday' UNION ALL SELECT 'Friday' UNION ALL SELECT 'Saturday') AS days_of_week"))
                ->leftJoin(DB::raw("(SELECT DAYNAME(created_at) AS day_of_week, AVG(reservoir_reading) AS avg_reservoir, AVG(trough_reading) AS avg_trough FROM feed_readings WHERE created_at BETWEEN '{$startDate}' AND '{$endDate}' GROUP BY day_of_week) AS averages"), 'days_of_week.day_of_week', '=', 'averages.day_of_week')
                ->select('days_of_week.day_of_week', DB::raw('COALESCE(averages.avg_reservoir, 0) AS avg_reservoir'), DB::raw('COALESCE(averages.avg_trough, 0) AS avg_trough'))
                ->get();
        }elseif ($interval === 'monthly') {
            $feed_level_data_sets = DB::table(DB::raw("(SELECT 'January' AS month UNION ALL SELECT 'February' UNION ALL SELECT 'March' UNION ALL SELECT 'April' UNION ALL SELECT 'May' UNION ALL SELECT 'June' UNION ALL SELECT 'July' UNION ALL SELECT 'August' UNION ALL SELECT 'September' UNION ALL SELECT 'October' UNION ALL SELECT 'November' UNION ALL SELECT 'December') AS months"))
                ->leftJoin(DB::raw("(SELECT MONTHNAME(created_at) AS month, AVG(reservoir_reading) AS avg_reservoir, AVG(trough_reading) AS avg_trough FROM feed_readings WHERE YEAR(created_at) = 2023 GROUP BY month) AS averages"), 'months.month', '=', 'averages.month')
                ->select('months.month', DB::raw('COALESCE(averages.avg_reservoir, 0) AS avg_reservoir'), DB::raw('COALESCE(averages.avg_trough, 0) AS avg_trough'))
                ->get();
        }elseif ($interval === 'yearly') {
            $feed_level_data_sets = DB::table('feed_readings')
                ->select(DB::raw('YEAR(created_at) AS year'), DB::raw('AVG(reservoir_reading) AS avg_reservoir'), DB::raw('AVG(trough_reading) AS avg_trough'))
                ->whereBetween(DB::raw('YEAR(created_at)'), [$startingYear, $currentYear])
                ->groupBy(DB::raw('YEAR(created_at)'))
                ->get();
        }

        // Convert the stdClass objects to FeedReading models
        $feed_level_data_sets = $feed_level_data_sets->map(function ($item) use ($interval) {
            $feedReading = new WaterReading();
            $feedReading->time = $interval === 'weekly' ? $item->day_of_week : ($interval === 'monthly' ? $item->month : $item->year);
            $feedReading->avg_reservoir = $item->avg_reservoir;
            $feedReading->avg_trough = $item->avg_trough;
            return $feedReading;
        });

        $labels = $feed_level_data_sets->map(function(WaterReading $feed_level_data_sets, $key) {
            return $feed_level_data_sets->time;
        });

        $datasets = new Collection([
            $feed_level_data_sets->map(function(WaterReading $feed_level_data_sets) {
                return number_format($feed_level_data_sets->avg_reservoir, 2, '.', '');
            }),

            $feed_level_data_sets->map(function(WaterReading $feed_level_data_sets) {
                return number_format($feed_level_data_sets->avg_trough, 2, '.', '');
            }),
        ]);

        return (new ChartComponentData($labels, $datasets));
    }
}
