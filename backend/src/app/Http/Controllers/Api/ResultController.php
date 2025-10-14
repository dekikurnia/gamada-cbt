<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Result;
use Illuminate\Http\Request;

class ResultController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Result::with(['exam', 'user'])->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'user_id' => 'required|exists:users,id',
            'score' => 'nullable|integer|min:0|max:100',
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

        return response()->json($result, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return response()->json($result->load(['exam', 'user']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'score' => 'required|integer|min:0|max:100',
        ]);

        $result->update($validated);
        return response()->json($result);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $result->delete();
        return response()->json(null, 204);
    }
}
