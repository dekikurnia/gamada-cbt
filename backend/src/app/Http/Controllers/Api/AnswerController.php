<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Answer;
use App\Models\Question;
use App\Models\Result;
use Illuminate\Http\Request;

class AnswerController extends Controller
{
    public function index()
    {
        return response()->json(Answer::with(['question', 'user'])->get());
    }

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

        // Hitung ulang skor
        $examId = $question->exam_id;
        $correctCount = Answer::where('user_id', $user->id)
            ->whereHas('question', fn($q) => $q->where('exam_id', $examId))
            ->where('is_correct', true)
            ->count();

        $totalQuestions = Question::where('exam_id', $examId)->count();
        $score = $totalQuestions ? round(($correctCount / $totalQuestions) * 100, 2) : 0;

        // Update ke tabel hasil
        Result::updateOrCreate(
            ['exam_id' => $examId, 'user_id' => $user->id],
            ['score' => $score],
        );

        return response()->json([
            'message' => 'Jawaban disimpan',
            'is_correct' => $isCorrect,
            'score' => $score,
        ]);
    }

    public function show(string $id)
    {
        $answer = Answer::with(['question', 'user'])->findOrFail($id);
        return response()->json($answer);
    }

    public function update(Request $request, string $id)
    {
        $answer = Answer::findOrFail($id);
        $validated = $request->validate([
            'response' => 'nullable',
        ]);
        $answer->update($validated);
        return response()->json($answer);
    }

    public function destroy(string $id)
    {
        $answer = Answer::findOrFail($id);
        $answer->delete();
        return response()->json(null, 204);
    }
}
