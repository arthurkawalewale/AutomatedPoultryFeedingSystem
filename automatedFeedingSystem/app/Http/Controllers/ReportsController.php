<?php

namespace App\Http\Controllers;

use App\Charts\FeedWeeklyChart;
use App\Models\WaterReading;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportsController extends Controller
{
    public function index(){

        // Get the start and end dates for the current week
        $startDate = Carbon::now()->startOfWeek();
        $endDate = Carbon::now()->endOfWeek();

        /*$weekly_data = DB::table('water_readings')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();*/

        $weekly_data = WaterReading::whereBetween('created_at', [$startDate, $endDate])->pluck('reservoir_reading', 'created_at');

        //dd($weekly_data);

        $weekly_chart = new FeedWeeklyChart;
        $weekly_chart->labels($weekly_data->keys());

        //dd($weekly_data->keys());
        $weekly_chart->dataset('Weekly Feed Stats', 'bar', $weekly_data->values())->backgroundColor('#0044cc');

        //dd($weekly_data->values());

        return view('welcome', compact('weekly_chart'));
    }
}
