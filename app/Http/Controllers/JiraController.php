<?php

namespace App\Http\Controllers;

use App\Services\JiraService;
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
}
