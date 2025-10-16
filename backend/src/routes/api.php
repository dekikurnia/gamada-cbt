<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ExamController;
use App\Http\Controllers\Api\QuestionController;
use App\Http\Controllers\Api\AnswerController;
use App\Http\Controllers\Api\ResultController;
use App\Http\Controllers\Api\DepartmentController;
use App\Http\Controllers\Api\ClassRoomController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

# ======================
# 🔐 AUTH
# ======================
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);


# ======================
# 🧑‍💼 ROLE: ADMIN
# ======================
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {

    // 🔹 CRUD Jurusan & Kelas
    Route::apiResource('departments', DepartmentController::class);
    Route::apiResource('classrooms', ClassRoomController::class);

    // 🔹 CRUD Ujian
    Route::apiResource('exams', ExamController::class)->except(['show']);

    // 🔹 Reset status login siswa
    Route::delete('/exams/{examId}/reset-user/{userId}', [ExamController::class, 'resetUser']);
});


# ======================
# 👩‍🏫 ROLE: GURU
# ======================
Route::middleware(['auth:sanctum', 'role:teacher'])->group(function () {

    // 🔹 CRUD Ujian milik guru
    Route::apiResource('exams', ExamController::class)->except(['show']);

    // 🔹 Kelola soal & hasil ujian
    Route::apiResource('questions', QuestionController::class);
    Route::apiResource('results', ResultController::class);

    // 🔹 Refresh token ujian
    Route::post('/exams/{id}/refresh-token', [ExamController::class, 'refreshToken']);
});


# ======================
# 🧑‍🎓 ROLE: SISWA
# ======================
Route::middleware(['auth:sanctum', 'role:student'])->group(function () {

    // 🔹 Siswa join ujian dengan TOKEN
    Route::post('/exams/token', [ExamController::class, 'joinByToken']);

    // 🔹 Kirim jawaban ujian
    Route::apiResource('answers', AnswerController::class)->only(['store', 'index']);
});
