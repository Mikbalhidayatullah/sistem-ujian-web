<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\QuestionBank;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class QuestionBankController extends Controller
{
    public function index(Request $request): View
    {
        $teacher = $request->user();
        $subjectId = $request->integer('subject');

        $subjects = $teacher->subjects()
            ->withCount('questionBanks')
            ->orderBy('name')
            ->get();

        $questionBanks = QuestionBank::query()
            ->where('teacher_id', $teacher->id)
            ->with(['subject', 'options'])
            ->when($subjectId, fn ($query) => $query->where('subject_id', $subjectId))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('teacher.question-bank.index', [
            'subjects' => $subjects,
            'questionBanks' => $questionBanks,
            'selectedSubject' => $subjectId ? $subjects->firstWhere('id', $subjectId) : null,
        ]);
    }

    public function destroy(Request $request, QuestionBank $questionBank): RedirectResponse
    {
        abort_unless($questionBank->teacher_id === $request->user()->id, 403);

        if ($questionBank->media_path) {
            \Illuminate\Support\Facades\Storage::delete($questionBank->media_path);
        }

        $questionBank->delete();

        return back()->with('status', 'Soal pada bank soal berhasil dihapus.');
    }
}
