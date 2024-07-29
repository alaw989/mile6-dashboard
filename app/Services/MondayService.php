<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;

class MondayService
{
    protected $client;
    protected $apiToken;

    public function __construct()
    {
        $this->client = new Client();
        $this->apiToken = env('MONDAY_API_TOKEN');
    }

    public function query($query)
    {
        try {
            $response = $this->client->request('POST', 'https://api.monday.com/v2', [
                'headers' => [
                    'Authorization' => $this->apiToken,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'query' => $query,
                ],
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            return $e->getMessage();
        }
    }

    public function getProjects()
    {
        $query = '{
              me {
                id
                name
                teams {
                  id
                  name
                  boards {
                    id
                    name
                  }
                }
              }
            }';

        return $this->query($query);
    }
}
