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
        return response()->json(Question::with('exam')->paginate(20));
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
        return response()->json($question);
    }
    
    public function update(Request $request, string $id)
    {
        $question = Question::findOrFail($id);
        
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
        $question->delete();
        return response()->json(null, 204);
    }
    
}
