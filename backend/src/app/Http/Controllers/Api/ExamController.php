<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamUserStatus;
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
        return response()->json(Exam::all());
    }
    
    /**
    * Store a newly created resource in storage.
    */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'duration' => 'required|integer',
        ]);
        
        $exam = Exam::create($data);
        return response()->json($exam, 201);
    }
    
    public function refreshToken($examId)
    {
        $exam = Exam::findOrFail($examId);
        
        // Cek apakah token sudah expired
        if ($exam->token_expired_at <= now()) {
            $newToken = strtoupper(Str::random(5));
            $exam->update([
                'token' => $newToken,
                'token_expired_at' => Carbon::now()->addMinutes(15),
            ]);
            return response()->json(['message' => 'Token baru dibuat', 'token' => $newToken]);
        }
        
        return response()->json(['message' => 'Token masih aktif', 'token' => $exam->token]);
    }
    /**
    * Display the specified resource.
    */
    public function show(string $id)
    {
        return response()->json($exam->load('questions'));
    }
    
    /**
    * Update the specified resource in storage.
    */
    public function update(Request $request, string $id)
    {
        $exam->update($request->all());
        return response()->json($exam);
    }
    
    /**
    * Remove the specified resource from storage.
    */
    public function destroy(string $id)
    {
        $exam->delete();
        return response()->json(null, 204);
    }
    
    public function joinByToken(Request $request)
    {
        $request->validate(['token' => 'required|string|max:10']);
        $user = $request->user();
        
        $exam = Exam::where('token', strtoupper($request->token))
        ->where('token_expired_at', '>', now())
        ->first();
        
        if (!$exam) {
            return response()->json(['message' => 'Token salah atau kedaluwarsa'], 404);
        }
        
        // Cek apakah siswa sudah aktif di ujian ini
        $status = ExamUserStatus::where('exam_id', $exam->id)
        ->where('user_id', $user->id)
        ->first();
        
        if ($status && $status->is_active) {
            return response()->json(['message' => 'Kamu masih tercatat sedang ujian, hubungi pengawas atau proktor untuk reset.'], 403);
        }
        
        // Buat status baru
        ExamUserStatus::updateOrCreate(
            ['exam_id' => $exam->id, 'user_id' => $user->id],
            ['is_active' => true]
        );
        
        return response()->json([
            'message' => 'Token valid, selamat mengerjakan ujian',
            'exam' => $exam
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
    
}
