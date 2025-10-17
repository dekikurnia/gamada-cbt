<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Result;
use Illuminate\Http\Request;

class ResultController extends Controller
{
    
    public function index()
    {
        $user = $request->user();
        
        $query = Result::with(['exam', 'user']);
        
        if ($user->role === 'student') {
            // Siswa: hanya lihat hasilnya sendiri
            $query->where('user_id', $user->id);
        } elseif ($user->role === 'teacher') {
            // Guru: hanya lihat hasil ujian yang dia buat
            $query->whereHas('exam', fn($q) => $q->where('teacher_id', $user->id));
        }
        
        return response()->json($query->paginate(20));
    }
    
    /**
    * Store a newly created resource in storage.
    */
    public function store(Request $request)
    {
        $data = $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'user_id' => 'required|exists:users,id',
            'score' => 'nullable|numeric|min:0|max:100',
        ]);
        
        // Cek apakah hasil sudah ada
        $result = Result::updateOrCreate(
            [
                'exam_id' => $data['exam_id'],
                'user_id' => $data['user_id']
            ],
            [
                'score' => $data['score'] ?? null
                ]
            );
            
            return response()->json($result->load(['exam', 'user']), 201);
        }
        
        public function show(string $id)
        {
            $result = Result::with(['exam', 'user'])->findOrFail($id);
            $user = $request->user();
            
            if ($user->role === 'student' && $result->user_id !== $user->id) {
                return response()->json(['message' => 'Anda tidak memiliki akses ke hasil ini'], 403);
            }
            
            if ($user->role === 'teacher' && $result->exam->teacher_id !== $user->id) {
                return response()->json(['message' => 'Anda tidak memiliki akses ke hasil ujian ini'], 403);
            }
            
            return response()->json($result);
        }
        
        public function update(Request $request, string $id)
        {
            $result = Result::findOrFail($id);
            
            $validated = $request->validate([
                'score' => 'required|numeric|min:0|max:100',
            ]);
            
            $result->update($validated);
            return response()->json($result);
        }
        
        public function destroy(string $id)
        {
            $result = Result::findOrFail($id);
            $result->delete();
            return response()->json(null, 204);
        }
        
        public function byUser(string $userId)
        {
            $user = auth()->user();
            
            // Admin boleh lihat semua
            if ($user->role === 'admin') {
                $results = Result::with(['exam', 'user'])
                ->where('user_id', $userId)
                ->get();
                
                return response()->json($results);
            }
            
            // Guru hanya boleh lihat hasil ujian yang dia buat
            $results = Result::with(['exam', 'user'])
            ->where('user_id', $userId)
            ->whereHas('exam', fn($q) => $q->where('teacher_id', $user->id))
            ->get();
            
            if ($results->isEmpty()) {
                return response()->json(['message' => 'Tidak ada hasil ujian yang dapat Anda akses untuk siswa ini'], 404);
            }
            
            return response()->json($results);
        }
        
        public function byExam(string $examId)
        {
            $exam = Exam::findOrFail($examId);
            $user = auth()->user();
            
            if ($user->role === 'teacher' && $exam->teacher_id !== $user->id) {
                return response()->json(['message' => 'Anda tidak memiliki akses untuk melihat hasil ujian ini'], 403);
            }
            
            $results = Result::with(['exam', 'user'])
            ->where('exam_id', $examId)
            ->get();
            
            return response()->json($results);
        }
        
    }
    