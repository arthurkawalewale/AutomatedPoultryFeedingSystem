<?php

namespace App\Support;
use Illuminate\View\view;
use Livewire\Component;

/**
 * Class ChartComponent
 *
 * @package App\Support
 */
abstract class ChartComponent extends Component
{

    /**
     * @var string|null
     */
    public ?string $chart_id = null;

    /**
     * @var string|null
     */
    public ?string $chart_data_checksum = null;

    /**
     * @return string
     */
    protected abstract function chartClass(): string;

    /**
     * @return ChartComponentData
     */
    protected abstract function chartData(): ChartComponentData;

    /**
     * @return string
     */
    protected abstract function view(): string;

    /**
     * @return \illuminate\View\view
     */
    public function render(): View
    {
        $chart_data = $this->chartData();

        if(!$this->chart_id)
        {
            $chart_class = $this->chartClass();

            $chart = new $chart_class($chart_data);

            $this->chart_id = $chart->id;
        }

        elseif($chart_data->checksum()!==$this->chart_data_checksum)
        {
            $this->emit('chartUpdate', $this->chart_id, $chart_data->labels(), $chart_data->datasets());
        }

        $this->chart_data_checksum = $chart_data->checksum();

        return view($this->view(), [
            'chart' => ($chart ?? null)
        ]);
    }

}
