<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamUserStatus;
use App\Models\Answer;
use App\Models\Result;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ExamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Exam::with('classRoom', 'teacher')->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'duration' => 'required|integer|min:1', // menit
            'class_id' => 'nullable|exists:classes,id',
            'teacher_id' => 'nullable|exists:users,id',
            'start_time' => 'nullable|date',
            'end_time' => 'nullable|date|after:start_time',
            'token' => 'nullable|string|max:10',
            'token_expired_at' => 'nullable|date',
        ]);

        // Jika token tidak disertakan, buat token unik
        if (empty($data['token'])) {
            $data['token'] = strtoupper(Str::random(5));
        }

        // Jika token_expired_at tidak disertakan, set 15 menit dari sekarang (opsional)
        if (empty($data['token_expired_at'])) {
            $data['token_expired_at'] = Carbon::now()->addMinutes(15);
        }

        $exam = Exam::create($data);

        return response()->json($exam, 201);
    }

    /**
     * Refresh token (guru/admin)
     */
    public function refreshToken($examId)
    {
        $exam = Exam::findOrFail($examId);

        // Pastikan hanya guru/admin (jika kamu belum menggunakan Spatie di sini, sesuaikan)
        if (!in_array(auth()->user()->role, ['teacher', 'admin'])) {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }

        // Kalau token belum pernah dibuat atau sudah kedaluwarsa -> buat baru
        if (!$exam->token_expired_at || $exam->token_expired_at->lte(now())) {
            $newToken = strtoupper(Str::random(5));
            $exam->update([
                'token' => $newToken,
                'token_expired_at' => Carbon::now()->addMinutes(15),
            ]);

            return response()->json(['message' => 'Token baru dibuat', 'token' => $newToken]);
        }

        return response()->json(['message' => 'Token masih aktif', 'token' => $exam->token]);
    }

    public function show(string $id)
    {
        $exam = Exam::with('questions')->findOrFail($id);
        return response()->json($exam);
    }


    public function update(Request $request, string $id)
    {
        $exam = Exam::findOrFail($id);

        $data = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'duration' => 'sometimes|required|integer|min:1',
            'class_id' => 'sometimes|nullable|exists:classes,id',
            'teacher_id' => 'sometimes|nullable|exists:users,id',
            'start_time' => 'sometimes|nullable|date',
            'end_time' => 'sometimes|nullable|date|after:start_time',
            'token' => 'sometimes|nullable|string|max:10',
            'token_expired_at' => 'sometimes|nullable|date',
        ]);

        $exam->update($data);
        return response()->json($exam);
    }

    public function destroy(string $id)
    {
        $exam = Exam::findOrFail($id);
        $exam->delete();
        return response()->json(null, 204);
    }

    public function joinByToken(Request $request)
    {
        $request->validate(['token' => 'required|string|max:10']);
        $user = $request->user();

        $tokenInput = strtoupper($request->token);

        $exam = Exam::where('token', $tokenInput)->first();

        if (!$exam) {
            return response()->json(['message' => 'Token salah atau tidak ditemukan'], 404);
        }

        // Cek token expired (null-safe)
        if ($exam->token_expired_at && $exam->token_expired_at->lte(now())) {
            return response()->json(['message' => 'Token sudah kedaluwarsa'], 403);
        }

        $now = now();

        // Cek window waktu global start/end
        if ($exam->start_time && $now->lt($exam->start_time)) {
            return response()->json(['message' => 'Ujian belum dimulai'], 403);
        }

        if ($exam->end_time && $now->gt($exam->end_time)) {
            return response()->json(['message' => 'Waktu ujian sudah berakhir'], 403);
        }

        // Cek apakah siswa sudah aktif di ujian ini
        $status = ExamUserStatus::where('exam_id', $exam->id)
            ->where('user_id', $user->id)
            ->first();

        if ($status && $status->is_active) {
            return response()->json(['message' => 'Kamu masih tercatat sedang ujian, hubungi pengawas atau proktor untuk reset.'], 403);
        }

        // Tandai siswa aktif (create atau re-activate)
        ExamUserStatus::updateOrCreate(
            ['exam_id' => $exam->id, 'user_id' => $user->id],
            ['is_active' => true]
        );

        // Hitung remaining time per siswa:
        // jika end_time diset secara global, remaining = min(duration*60, end_time - now)
        $durationSeconds = $exam->duration * 60;
        if ($exam->end_time) {
            $secondsUntilEnd = $exam->end_time->diffInSeconds($now, false);
            $remaining = $secondsUntilEnd > 0 ? min($durationSeconds, $secondsUntilEnd) : 0;
        } else {
            $remaining = $durationSeconds;
        }

        // Ambil soal (terbatas fields supaya tidak kirim jawaban benar)
        $questions = $exam->questions()->select('id', 'content', 'type', 'options')->get();

        return response()->json([
            'message' => 'Token valid, selamat mengerjakan ujian',
            'exam' => $exam,
            'remaining_time' => $remaining > 0 ? $remaining : 0,
            'questions' => $questions,
        ]);
    }

    public function resetUser($examId, $userId)
    {
        $exam = Exam::findOrFail($examId);

        // Pastikan hanya guru/admin
        if (!in_array(auth()->user()->role, ['teacher', 'admin'])) {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }

        $status = ExamUserStatus::where('exam_id', $examId)
            ->where('user_id', $userId)
            ->first();

        if (!$status) {
            return response()->json(['message' => 'Siswa tidak terdaftar di ujian ini'], 404);
        }

        $status->delete();

        return response()->json(['message' => 'Status login siswa berhasil direset']);
    }


    public function autoSubmit(Request $request)
    {
        $user = $request->user();
        $examId = $request->input('exam_id');

        // Validasi sederhana
        $request->validate([
            'exam_id' => 'required|exists:exams,id',
        ]);

        // Ambil semua jawaban siswa untuk ujian ini
        $answers = Answer::where('user_id', $user->id)
            ->whereHas('question', fn($q) => $q->where('exam_id', $examId))
            ->get();

        // Kalkulasi skor
        $score = $this->calculateScore($examId, $user->id);

        // Simpan hasil ke tabel results
        Result::updateOrCreate(
            ['exam_id' => $examId, 'user_id' => $user->id],
            ['score' => $score]
        );

        // Tandai siswa tidak aktif (sudah submit)
        ExamUserStatus::where('exam_id', $examId)
            ->where('user_id', $user->id)
            ->update(['is_active' => false]);

        return response()->json(['message' => 'Jawaban otomatis disubmit', 'score' => $score]);
    }

    /**
     * Hitung score (otomatis untuk tipe yang bisa dinilai otomatis)
     * Mengembalikan skor dalam persen (0-100), dibulatkan integer.
     */
    private function calculateScore(int $examId, int $userId): int
    {
        // total soal ujian
        $totalQuestions = \App\Models\Question::where('exam_id', $examId)->count();

        if ($totalQuestions === 0) {
            return 0;
        }

        // jumlah jawaban benar (is_correct = true)
        $correctCount = Answer::where('user_id', $userId)
            ->whereHas('question', fn($q) => $q->where('exam_id', $examId))
            ->where('is_correct', true)
            ->count();

        $score = round(($correctCount / $totalQuestions) * 100, 2);

        return $score;
    }
}
