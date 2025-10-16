<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Answer;
use Illuminate\Http\Request;

class AnswerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Answer::with(['question', 'user', 'matching', 'ordering'])->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'question_id' => 'required|exists:questions,id',
            'response' => 'required',
        ]);

        $user = $request->user();
        $question = Question::findOrFail($request->question_id);

        // Default nilai
        $isCorrect = null;

        // Jika soal pilihan ganda atau benar-salah → auto check
        if (in_array($question->type, ['multiple_choice', 'true_false'])) {
            $correctAnswer = $question->answer;
            $response = $request->response;

            // Samakan format (kalau JSON)
            if (is_array($correctAnswer)) {
                $isCorrect = json_encode($correctAnswer) === json_encode($response);
            } else {
                $isCorrect = $correctAnswer == $response;
            }
        }

        // Simpan jawaban
        $answer = Answer::updateOrCreate(
            [
                'user_id' => $user->id,
                'question_id' => $question->id,
            ],
            [
                'response' => $request->response,
                'is_correct' => $isCorrect,
            ]
        );

        // Update skor di tabel results
        $examId = $question->exam_id;
        $correctCount = Answer::where('user_id', $user->id)
            ->whereIn('question_id', $question->exam->questions->pluck('id'))
            ->where('is_correct', true)
            ->count();

        $totalQuestions = $question->exam->questions->count();
        $score = $totalQuestions ? round(($correctCount / $totalQuestions) * 100) : 0;

        Result::updateOrCreate(
            [
                'exam_id' => $examId,
                'user_id' => $user->id,
            ],
            [
                'score' => $score,
            ]
        );

        return response()->json([
            'message' => 'Jawaban disimpan',
            'is_correct' => $isCorrect,
            'score' => $score,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return response()->json($answer->load(['question', 'user']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'response' => 'nullable|array',
        ]);

        $answer->update($validated);
        return response()->json($answer);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $answer->delete();
        return response()->json(null, 204);
    }
}
