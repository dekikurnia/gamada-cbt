<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ExamController;

Route::apiResource('exams', ExamController::class);
Route::apiResource('questions', QuestionController::class);
