<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

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

    public function getData(): void
    {
        try {
            $response = $this->client
                ->request('GET','/v1/worker?access_key=r_dZQDQ9FStM9lZ&puid=441535')
                ->getBody()
                ->getContents();
            $response = json_decode($response, 1);
            d($response);
        }
        catch (GuzzleException $exception)
        {
            dd($exception);
        }
        $this->writeToModel($response['data']['data']);
    }

    private function writeToModel(array $data): void
    {
        foreach ($data as $item)
        {
            $row = new $this->modelClass();
            $row->worker_id = $item['worker_id'];
            $row->worker_name = $item['worker_name'];
            $row->date = strtotime("now");
            $row->hashrate = $item['shares_1d'];
            $row->reject = $item['reject_percent_1d'];
            $row->save();
        }
    }
}
