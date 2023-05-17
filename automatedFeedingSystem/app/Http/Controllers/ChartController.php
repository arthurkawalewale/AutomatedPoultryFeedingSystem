<?php

namespace App\Http\Controllers;

use App\Charts\waterTank;
use Illuminate\Http\Request;

class ChartController extends Controller
{
    public function index()
    {
        $chartData = new waterTank;

        $chartData = [
            "chart" => [
                "caption" => "Sales Performance",
                "subcaption" => "Current Month",
                "lowerLimit" => "0",
                "upperLimit" => "100",
                "theme" => "fusion",
                "showValue" => "1",
                "cylFillColor" => "#0075c2",
                "annotations" => [
                    "groups" => [
                        [
                            "items" => [
                                [
                                    "id" => "label",
                                    "type" => "text",
                                    "text" => "75%",
                                    "color" => "#666666",
                                    "fontSize" => "20",
                                    "x" => "100",
                                    "y" => "70 - 40"
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            "value" => "75"
        ];

        return view('welcome', compact('chartData'));
    }
}
