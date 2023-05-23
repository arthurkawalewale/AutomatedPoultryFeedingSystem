<?php

namespace App\Http\Livewire;

use App\Charts\FeedLevelChart;
use App\Models\FeedReservoirReading;
use App\Support\ChartComponent;
use App\Support\ChartComponentData;
use Illuminate\Support\Collection;
use Livewire\Component;

class FeedLevelDatasets extends ChartComponent
{
    /*public function render()
    {
        return view('livewire.feed-level-datasets');
    }*/

    /**
     * @return string
     */
    protected function view(): string
    {
        return 'livewire.feed-level-datasets';
    }

    /**
     * @return string
     */
    protected function chartClass(): string
    {
        return FeedLevelChart::class;
    }

    /**
     * @return \App\Support\ChartComponentData
     */
    protected function chartData(): ChartComponentData
    {
        $feed_level_datasets = FeedReservoirReading::query()->latest('id')->orderBy('id','desc')->take(5)->get();

        $labels = $feed_level_datasets->map(function(FeedReservoirReading $feed_level_datasets, $key) {
            return $feed_level_datasets->created_at->format('H:i');
        });

        $labels = $labels->reverse()->values();

        $datasets = new Collection([
            $feed_level_datasets->map(function(FeedReservoirReading $feed_level_datasets) {
                return number_format($feed_level_datasets->reading, 2, '.', '');
            })->reverse()->values(),
        ]);

        return (new ChartComponentData($labels, $datasets));
    }
}
