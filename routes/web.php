<?php

use App\Http\Controllers\MondayController;
use Illuminate\Support\Facades\Route;

Route::get('/', [MondayController::class, 'getBoards']);
