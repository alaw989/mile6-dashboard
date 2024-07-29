<?php

namespace App\Http\Controllers;

use App\Services\JiraService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
        return response()->json($issue);
    }
}
