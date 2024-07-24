<?php

use App\Http\Controllers\Api\InputController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::apiResource('inputs', InputController::class);
