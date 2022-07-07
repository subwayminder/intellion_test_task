<?php

namespace App\Services\TimeJobs;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class BtcPoolExchange
{
    private Client $client;
    private string $modelClass;

    public function __construct(string $modelClass)
    {
        $this->client = new Client([
            'base_uri' => 'https://pool.api.btc.com/',
            'timeout' => 10.0
        ]);
        $this->modelClass = $modelClass;
    }

    public function fetchData(int $page = 1): void
    {
        $result = [];
        try {
            $response = $this->client
                ->request('GET', '/v1/worker?access_key='.config('services.btcpool.key').'&page='.$page)
                ->getBody()
                ->getContents();
        } catch (GuzzleException $exception) {
            Log::error('Request failed: '. $exception->getMessage());
        }
        $response = json_decode($response, 1);
        $result[] = $response;
        if ($response['err_no'] === 0)
        {
            if ($response['data']['page'] < $response['data']['page_count'])
            {
                $this->fetchData($page++);
            }
            $this->writeToModel($this->buildResponseResult($result));
        }
        else Log::error('Request error: '. $response['err_msg']);
    }

    private function buildResponseResult(array $response): array
    {
        $result = [];
        foreach ($response as $page)
        {
            foreach ($page['data']['data'] as $row){
                $result[] = $row;
            }
        }
        return $result;
    }

    private function writeToModel(array $data): void
    {
        foreach ($data as $item) {
            /** @var Model $row */
            $row = $this->modelClass::where('worker_id', $item['worker_id'])
                ->where('date', date('Y-m-d', $item['last_share_time']))->first();
            $row = $row ?: new $this->modelClass();
            $row->worker_id = $item['worker_id'];
            $row->worker_name = $item['worker_name'];
            $row->date = date('Y-m-d', $item['last_share_time']);
            $row->hashrate = $item['shares_1d'];
            $row->reject = $item['reject_percent_1d'];
            $row->save();
        }
    }
}
