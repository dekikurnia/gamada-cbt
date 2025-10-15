<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ExamController;
use App\Http\Controllers\Api\QuestionController;
use App\Http\Controllers\Api\AnswerController;
use App\Http\Controllers\Api\ResultController;
use App\Http\Controllers\Api\AuthController;

Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

Route::middleware('auth:sanctum')->group(function () {

    // === Exam Custom Routes ===
    // 1️⃣ Guru/Admin bisa refresh token ujian
    Route::post('/exams/{id}/refresh-token', [ExamController::class, 'refreshToken']);
    
    // 2️⃣ Siswa join ke ujian pakai token
    Route::post('/exams/token', [ExamController::class, 'joinByToken']);

    // 🔄 Reset status login siswa (guru/admin)
    Route::delete('/exams/{examId}/reset-user/{userId}', [ExamController::class, 'resetUser']);

    // === Resource Routes ===
    Route::apiResource('departments', DepartmentController::class);
    Route::apiResource('classrooms', ClassRoomController::class);
    Route::apiResource('exams', ExamController::class);
    Route::apiResource('questions', QuestionController::class);
    Route::apiResource('answers', AnswerController::class);
    Route::apiResource('results', ResultController::class);
});

