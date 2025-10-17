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
            $query->where('user_id', $user->id);
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
            // Cek apakah user ada
            $results = Result::with(['exam', 'user'])
            ->where('user_id', $userId)
            ->get();
            
            if ($results->isEmpty()) {
                return response()->json(['message' => 'Data hasil ujian tidak ditemukan untuk siswa ini'], 404);
            }
            
            return response()->json($results);
        }
        
        public function byExam(string $examId)
        {
            $results = Result::with(['exam', 'user'])
            ->where('exam_id', $examId)
            ->get();
            
            if ($results->isEmpty()) {
                return response()->json(['message' => 'Belum ada hasil untuk ujian ini'], 404);
            }
            
            return response()->json($results);
        }
        
    }
    