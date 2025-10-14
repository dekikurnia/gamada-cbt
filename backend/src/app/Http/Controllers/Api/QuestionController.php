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
        return response()->json(Question::with('exam')->get());
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

        return response()->json($question, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return response()->json($question->load('exam'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'type' => 'sometimes|required|in:multiple_choice,essay,true_false,matching,ordering',
            'content' => 'sometimes|required|string',
            'options' => 'nullable|array',
            'answer' => 'nullable|array',
        ]);

        $question->update($validated);

        return response()->json($question);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
         $question->delete();

        return response()->json(null, 204);
    }
}
