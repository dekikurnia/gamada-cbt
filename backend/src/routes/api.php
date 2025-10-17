<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ExamController;
use App\Http\Controllers\Api\QuestionController;
use App\Http\Controllers\Api\AnswerController;
use App\Http\Controllers\Api\ResultController;
use App\Http\Controllers\Api\DepartmentController;
use App\Http\Controllers\Api\ClassRoomController;
use App\Http\Controllers\Api\UserController;

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
# 🧑‍💼 ADMIN
# ======================
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {

    // 🔹 CRUD Jurusan & Kelas
    Route::apiResource('departments', DepartmentController::class);
    Route::apiResource('classrooms', ClassRoomController::class);

    // 🔹 CRUD Ujian, Soal, dan Hasil
    Route::apiResource('exams', ExamController::class)->except(['show']);
    Route::apiResource('questions', QuestionController::class);
    Route::apiResource('results', ResultController::class);

    // 🔹 Reset status login siswa
    Route::delete('/exams/{examId}/reset-user/{userId}', [ExamController::class, 'resetUser']);
});


# ======================
# 👩‍🏫 GURU & ADMIN (Gabungan)
# ======================
Route::middleware(['auth:sanctum', 'role:teacher|admin'])->group(function () {

    // 🔹 CRUD Ujian milik guru/admin
    Route::apiResource('exams', ExamController::class)->except(['show']);

    // 🔹 Refresh token ujian
    Route::post('/exams/{id}/refresh-token', [ExamController::class, 'refreshToken']);

    // 🔹 Kelola soal & hasil ujian
    Route::apiResource('questions', QuestionController::class);
    Route::apiResource('results', ResultController::class)->only(['index', 'show', 'update']);

    // 🔹 Rekap hasil per siswa dan per ujian
    Route::get('/results/user/{userId}', [ResultController::class, 'byUser']);
    Route::get('/results/exam/{examId}', [ResultController::class, 'byExam']);
});


# ======================
# 🧑‍🎓 SISWA
# ======================
Route::middleware(['auth:sanctum', 'role:student'])->group(function () {

    // 🔹 Siswa join ujian dengan TOKEN
    Route::post('/exams/token', [ExamController::class, 'joinByToken']);

    // 🔹 Kirim jawaban ujian
    Route::apiResource('answers', AnswerController::class)->only(['store', 'index']);

    // 🔹 Auto submit (jika waktu habis)
    Route::post('/answers/auto-submit', [AnswerController::class, 'autoSubmit']);
});

Route::middleware('auth:sanctum')->get('/me', [UserController::class, 'me']);
