<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Question;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    /**
    * Display a listing of the resource.
    */
    public function index()
    {
        $user = $request->user();
        
        $query = Question::with('exam');
        
        // Guru hanya boleh lihat soal di ujian yang dia buat
        if ($user->role === 'teacher') {
            $query->whereHas('exam', fn($q) => $q->where('teacher_id', $user->id));
        }
        
        return response()->json($query->paginate(20));
    }
    
    /**
    * Store a newly created resource in storage.
    */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'type' => 'required|in:multiple_choice,essay,true_false,matching,ordering',
            'content' => 'required|string',
            'options' => 'nullable|array',
            'answer' => 'nullable|array',
        ]);
        
        $user = $request->user();
        // Guru tidak boleh buat soal di ujian orang lain
        if ($user->role === 'teacher' && $exam->teacher_id !== $user->id) {
            return response()->json(['message' => 'Anda tidak memiliki akses untuk menambahkan soal pada ujian ini'], 403);
        }
        $question = Question::create([
            'exam_id' => $validated['exam_id'],
            'type' => $validated['type'],
            'content' => $validated['content'],
            'options' => $validated['options'] ?? null,
            'answer' => $validated['answer'] ?? null,
        ]);
        
        return response()->json($question->load('exam'), 201);
    }
    
    public function show(string $id)
    {
        $question = Question::with('exam')->findOrFail($id);
        $user = $request->user();
        
        // Guru hanya boleh lihat soal miliknya
        if ($user->role === 'teacher' && $question->exam->teacher_id !== $user->id) {
            return response()->json(['message' => 'Anda tidak memiliki akses ke soal ini'], 403);
        }
        
        return response()->json($question);
    }
    
    public function update(Request $request, string $id)
    {
        $question = Question::findOrFail($id);
        $user = $request->user();
        
        // Cek kepemilikan ujian
        if ($user->role === 'teacher' && $question->exam->teacher_id !== $user->id) {
            return response()->json(['message' => 'Anda tidak memiliki akses ke soal ini'], 403);
        }
        
        $validated = $request->validate([
            'type' => 'sometimes|required|in:multiple_choice,essay,true_false,matching,ordering',
            'content' => 'sometimes|required|string',
            'options' => 'nullable',
            'answer' => 'nullable',
        ]);
        
        $question->update($validated);
        
        return response()->json($question);
    }
    
    public function destroy(string $id)
    {
        $question = Question::findOrFail($id);
        $user = auth()->user();
        
        // Cek kepemilikan
        if ($user->role === 'teacher' && $question->exam->teacher_id !== $user->id) {
            return response()->json(['message' => 'Anda tidak memiliki akses untuk menghapus soal ini'], 403);
        }
        
        $question->delete();
        return response()->json(null, 204);
    }
    
}
