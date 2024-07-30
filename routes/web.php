<?php

use App\Http\Controllers\JiraController;
use App\Http\Controllers\MondayController;
use Illuminate\Support\Facades\Route;

Route::get('/', [MondayController::class, 'getBoards']);
Route::get('/jira/projects', [JiraController::class, 'getAllProjects']);
Route::get('/jira/{issueKey}', [JiraController::class, 'getIssue'])->where('issueKey', '^(?!projects$).*');
