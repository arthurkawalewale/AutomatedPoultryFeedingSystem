<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ChartController extends Controller
{
    public function index()
    {
        $chartData = [
            ['label' => 'January', 'value' => '100'],
            ['label' => 'February', 'value' => '200'],
            ['label' => 'March', 'value' => '150'],
            ['label' => 'April', 'value' => '300'],
            ['label' => 'May', 'value' => '250'],
        ];

        return view('index', compact('chartData'));
    }
}
