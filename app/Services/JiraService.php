<?php

// app/Services/JiraService.php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Http;
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

    public function getTasksAssignedToMe()
    {
        $jql = 'assignee = currentUser() AND resolution = Unresolved ORDER BY priority DESC, updated DESC';
        $url = $this->baseUrl . '/rest/api/3/search';

        try {
            $response = $this->client->request('GET', $url, [
                'headers' => [
                    'Authorization' => 'Basic ' . base64_encode($this->email . ':' . $this->apiToken),
                    'Accept' => 'application/json',
                ],
                'query' => [
                    'jql' => $jql,
                    'fields' => 'summary,status,assignee,project',
                ],
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            Log::error('Error fetching tasks: ' . $e->getMessage());
            $responseBody = $e->hasResponse() ? json_decode($e->getResponse()->getBody()->getContents(), true) : null;

            return [
                'message' => 'An error occurred',
                'status_code' => $e->getCode(),
                'response_body' => $responseBody,
            ];
        }
    }

    public function searchIssues($jql)
    {
        $url = "https://your-domain.atlassian.net/rest/api/3/search?jql=" . urlencode($jql);
        $response = $this->makeRequest('GET', $url);
        return $response;
    }

    private function makeRequest($method, $url, $body = [])
    {
        $response = Http::withBasicAuth($this->username, $this->apiToken)
            ->$method($url, $body);

        if ($response->failed()) {
            throw new \Exception('Failed to connect to Jira API: ' . $response->body());
        }

        return $response->json();
    }

    public function getAllBoards()
    {
        $url = $this->baseUrl . '/rest/agile/1.0/board';

        try {
            $response = $this->client->request('GET', $url, [
                'headers' => [
                    'Authorization' => 'Basic ' . base64_encode($this->email . ':' . $this->apiToken),
                    'Accept' => 'application/json',
                ],
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            Log::error('Error fetching boards: ' . $e->getMessage());
            return $this->handleRequestException($e);
        }
    }

    public function getSprintsByBoard($boardId)
    {
        $url = $this->baseUrl . "/rest/agile/1.0/board/{$boardId}/sprint";

        try {
            $response = $this->client->request('GET', $url, [
                'headers' => [
                    'Authorization' => 'Basic ' . base64_encode($this->email . ':' . $this->apiToken),
                    'Accept' => 'application/json',
                ],
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            Log::error('Error fetching sprints: ' . $e->getMessage());
            return $this->handleRequestException($e);
        }
    }

    public function getAllSprintsAndTasks()
    {
        $startTime = microtime(true);

        $boards = $this->jiraService->getAllBoards();
        $tasks = [];
        $supportedBoards = [];

        // Filter boards to only include those that support sprints (e.g., Scrum boards)
        foreach ($boards['values'] as $board) {
            if ($board['type'] !== 'kanban') {
                $supportedBoards[] = $board;
            }
        }

        Log::info("Filtered boards count: " . count($supportedBoards));

        // Iterate through the supported boards to get sprints and tasks
        foreach ($supportedBoards as $board) {
            $sprints = $this->jiraService->getSprintsByBoard($board['id']);
            foreach ($sprints['values'] as $sprint) {
                $sprintTasks = $this->jiraService->getTasksAssignedToMeInSprint($sprint['id']);
                if (isset($sprintTasks['issues'])) {
                    $tasks = array_merge($tasks, $sprintTasks['issues']);
                }
            }
        }

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;
        Log::info("Total execution time: " . $executionTime . " seconds");

        // Create a response array with boards and their tasks
        $response = [
            'total_boards' => count($supportedBoards),
            'boards' => $supportedBoards,
            'total_tasks' => count($tasks),
            'tasks' => $tasks
        ];

        // Return the response as pretty-printed JSON
        return response()->json($response, 200, [], JSON_PRETTY_PRINT);
    }

    public function getTasksAssignedToMeInSprint($sprintId)
    {
        $jql = 'assignee = currentUser() AND sprint = ' . $sprintId;
        $url = $this->baseUrl . '/rest/api/3/search';

        try {
            $response = $this->client->request('GET', $url, [
                'headers' => [
                    'Authorization' => 'Basic ' . base64_encode($this->email . ':' . $this->apiToken),
                    'Accept' => 'application/json',
                ],
                'query' => [
                    'jql' => $jql,
                    'fields' => 'summary,status,assignee,project',
                ],
                'timeout' => 200, // Increase timeout limit
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            Log::error('Error fetching tasks: ' . $e->getMessage());
            return $this->handleRequestException($e);
        }
    }

    private function handleRequestException(RequestException $e)
    {
        $responseBody = $e->hasResponse() ? json_decode($e->getResponse()->getBody()->getContents(), true) : null;

        return [
            'message' => 'An error occurred',
            'status_code' => $e->getCode(),
            'response_body' => $responseBody,
        ];
    }


}
