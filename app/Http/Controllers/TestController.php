<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Services\BtcPoolExchange;
use App\Models\Worker;

class TestController extends Controller
{
    public function index()
    {
        (new BtcPoolExchange(Worker::class))->getData();
        d(Worker::all());
    }
}
