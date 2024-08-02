<?php

namespace App\Http\Controllers;

use App\Services\JiraService;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class JiraController extends Controller
{
    protected $jiraService;

    public function __construct(JiraService $jiraService)
    {
        $this->jiraService = $jiraService;
    }

    public function getIssue($issueKey)
    {
        $issue = $this->jiraService->getIssue($issueKey);

        return Inertia::render('Welcome', ['data' => $issue]);

    }

    public function getAllProjects()
    {
        $projects = $this->jiraService->getAllProjects();
        return response()->json($projects);
    }

    public function getTasksAssignedToMe()
    {
        $tasks = $this->jiraService->getTasksAssignedToMe();
        return Inertia::render('Welcome', ['data' => $tasks]);
    }

    public function getIssuesBySprint($sprintId)
    {
        $jql = 'sprint = ' . $sprintId;
        $response = $this->jiraService->searchIssues($jql);
        return response()->json($response);
    }

    public function getAllSprintsAndTasks()
    {
        $tasks = [];
        $boards = config('services.jira.board_numbers');

        foreach ($boards as $board) {
            $sprints = $this->jiraService->getSprintsByBoard($board);

            foreach ($sprints['values'] as $sprint) {
                $sprintTasks = $this->jiraService->getTasksAssignedToMeInSprint($sprint['id']);

                if (isset($sprintTasks['issues'])) {
                    foreach ($sprintTasks['issues'] as $issue) {
                        $tasks[] = [
                            'id' => $issue['id'],
                            'key' => $issue['key'],
                            'summary' => $issue['fields']['summary'],
                            'project' => [
                                'id' => $issue['fields']['project']['id'],
                                'name' => $issue['fields']['project']['name'],
                            ],
                            'assignee' => [
                                'displayName' => $issue['fields']['assignee']['displayName'],
                            ],
                            'status' => [
                                'statusCategory' => [
                                    'id' => $issue['fields']['status']['statusCategory']['id'],
                                    'key' => $issue['fields']['status']['statusCategory']['key'],
                                    'colorName' => $issue['fields']['status']['statusCategory']['colorName'],
                                    'name' => $issue['fields']['status']['statusCategory']['name'],
                                ],
                            ],
                            'sprint' => [
                                'id' => $sprint['id'],
                                'name' => $sprint['name'],
                                'state' => $sprint['state'],
                            ],
                        ];
                    }
                }
            }
        }

        // Create a response array with boards and their tasks
        $response = [
            'total_boards' => count($boards),
            'boards' => $boards,
            'total_tasks' => count($tasks),
            'tasks' => $tasks
        ];

        return Inertia::render('Welcome', ['data' => $response]);
    }

}
