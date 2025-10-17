<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Exam;

class OwnsExam
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $examId = $request->route('examId') ?? $request->route('id');

        if ($user->role === 'teacher' && $examId) {
            $exam = Exam::find($examId);

            if (!$exam || $exam->teacher_id !== $user->id) {
                return response()->json(['message' => 'Anda tidak memiliki akses ke ujian ini'], 403);
            }
        }

        return $next($request);
    }
}
