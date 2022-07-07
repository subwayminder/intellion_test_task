<?php

namespace App\Http\Controllers;

use App\Models\Worker;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        return view('main', ['result' => []]);
    }

    public function show(Request $request)
    {
        $fields = $request->validate([
            'tariff' => 'required|numeric',
            'consumption' => 'required|numeric',
            'date_start' => 'required|date',
            'date_end' => 'required|date',
        ]);

        $workersData = Worker::whereBetween('date', [$fields['date_start'], $fields['date_end']])->get()->toArray();
        if ($workersData){
            $result = $this->buildRenderData($workersData, $fields['tariff'], $fields['consumption']);
            return view('main', ['result' => $result]);
        }
        else return view('main', ['result' => []]);

    }

    private function buildRenderData(array $rows, float $tariff, float $consumption): array
    {
        $result = [];
        $total = 0;
        foreach ($rows as $row) {
            $daySum = ($tariff * $consumption * 24 / 13.5) * $row['hashrate'];
            $result['rows'][$row['worker_name']]['data_by_date'][$row['date']] = [
                'hashrate' => $row['hashrate'] . ' Th/s',
                'day_sum' => $daySum
            ];
            if (array_key_exists('total', $result['rows'][$row['worker_name']])) $result['rows'][$row['worker_name']]['total'] += $daySum;
            else $result['rows'][$row['worker_name']]['total'] = $daySum;
            $total += $daySum;
        }
        $result['total'] = $total;
        return $result;
    }
}
