<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;

class JiraService
{
    protected $client;
    protected $baseUrl;
    protected $apiToken;
    protected $email;

    public function __construct()
    {
        $this->client = new Client();
        $this->baseUrl = env('JIRA_BASE_URL');
        $this->apiToken = env('JIRA_API_TOKEN');
        $this->email = env('JIRA_EMAIL');
    }

    public function getIssue($issueKey)
    {
        try {
            $response = $this->client->request('GET', $this->baseUrl . '/rest/api/3/issue/' . $issueKey, [
                'auth' => [$this->email, $this->apiToken],
                'headers' => [
                    'Accept' => 'application/json',
                ],
            ]);

            return json_decode($response->getBody()->getContents());
        } catch (RequestException $e) {
            $response = $e->getResponse();
            $statusCode = $response ? $response->getStatusCode() : 'N/A';
            $body = $response ? $response->getBody()->getContents() : 'N/A';

            Log::error('Jira API error', [
                'message' => $e->getMessage(),
                'status_code' => $statusCode,
                'url' => $this->baseUrl . '/rest/api/3/issue/' . $issueKey,
                'response_body' => $body
            ]);

            return response()->json([
                'message' => 'An error occurred',
                'status_code' => $statusCode,
                'response_body' => json_decode($body)
            ], $statusCode);
        }
    }
}
