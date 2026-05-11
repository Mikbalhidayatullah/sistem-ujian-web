<?php

namespace App\Http\Controllers;

use App\Models\ExamAttempt;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class QuestionMediaController extends Controller
{
    public function show(Request $request, Question $question)
    {
        abort_unless($question->media_path, 404);

        $question->loadMissing('exam');
        $user = $request->user();

        if ($user) {
            abort_unless($user->isAdmin() || $question->exam->teacher_id === $user->id, 403);

            return Storage::response($question->media_path, headers: [
                'X-Content-Type-Options' => 'nosniff',
                'Cache-Control' => 'private, no-store, max-age=0',
            ]);
        }

        $allowedAttemptIds = array_keys($request->session()->get('guest_allowed_attempts', []));

        abort_unless(
            $allowedAttemptIds !== [] && ExamAttempt::query()
                ->whereIn('id', $allowedAttemptIds)
                ->where('exam_id', $question->exam_id)
                ->exists(),
            403
        );

        return Storage::response($question->media_path, headers: [
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, no-store, max-age=0',
        ]);
    }
}
