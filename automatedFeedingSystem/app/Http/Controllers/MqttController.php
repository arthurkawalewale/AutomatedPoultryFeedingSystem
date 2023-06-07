<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use PhpMqtt\Client\Facades\MQTT;
use PhpMqtt\Client\MqttClient;

class MqttController extends Controller
{
    public function sendMessage()
    {
        $mqtt = new MqttClient('tcp://broker.example.com', 1883, 'client_id');
        $mqtt->connect();

        $topic = 'your/topic';
        $message = 'Hello, MQTT!';

        $mqtt->publish($topic, $message);

        $mqtt->disconnect();
    }
}

