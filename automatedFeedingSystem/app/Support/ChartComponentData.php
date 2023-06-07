<?php

namespace App\Support;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;
use PhpParser\Node\Scalar\String_;

/**
 * Class ChartComponentData
 *
 * @package App\Support
 */
class ChartComponentData implements Arrayable
{

    /**
     * @var \Illuminate\Support\Collection
     */
    private Collection $labels;

    /**
     * @var String
     */
    private String $interval;

    /**
     * @var \Illuminate\Support\Collection
     */
    private Collection $datasets;

    /**
     * ChartComponentData constructor.
     *
     * @param \Illuminate\Support\Collection $labels
     * @param \Illuminate\Support\Collection $datasets
     */
    public function __construct(Collection $labels, Collection $datasets)
    {
        $this->labels = $labels;
        $this->datasets = $datasets;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'labels'    => $this->labels,
            'datasets'  => $this->datasets
        ];
    }

    /**
     * @return string
     */
    public function checksum(): string
    {
        return md5(json_encode($this->toArray()));
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function labels(): Collection
    {
        return $this->labels;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function datasets(): Collection
    {
        return $this->datasets;
    }

    public function interval(): String
    {
        return $this->interval;
    }
}
