<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\ExamAttempt;
use App\Models\ExamViolation;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $teacher = auth()->user();

        $subjects = $teacher->subjects()
            ->withCount([
                'exams' => fn ($query) => $query->whereNull('archived_at'),
            ])
            ->latest()
            ->get();
        $exams = $teacher->exams()
            ->whereNull('archived_at')
            ->with(['subject'])
            ->withCount(['questions', 'attempts'])
            ->latest()
            ->get();

        $recentViolations = ExamViolation::query()
            ->whereHas('attempt.exam', fn ($query) => $query->where('teacher_id', $teacher->id))
            ->with(['attempt.student', 'attempt.exam.subject'])
            ->latest('happened_at')
            ->take(5)
            ->get();

        $totalViolations = ExamViolation::query()
            ->whereHas('attempt.exam', fn ($query) => $query->where('teacher_id', $teacher->id))
            ->count();

        $stats = [
            'total_subjects' => $subjects->count(),
            'total_exams' => $exams->count(),
            'active_exams' => $exams->filter(fn ($exam) => $exam->isOpenNow())->count(),
            'scheduled_exams' => $exams->filter(fn ($exam) => $exam->start_at?->isFuture())->count(),
            'total_attempts' => (int) $exams->sum('attempts_count'),
            'total_questions' => (int) $exams->sum('questions_count'),
            'total_violations' => $totalViolations,
        ];

        return view('teacher.dashboard', [
            'subjects' => $subjects,
            'exams' => $exams,
            'recentViolations' => $recentViolations,
            'stats' => $stats,
        ]);
    }

    public function monitoring(): View
    {
        $teacher = auth()->user();

        $violations = ExamViolation::query()
            ->whereHas('attempt.exam', fn ($query) => $query->where('teacher_id', $teacher->id))
            ->with(['attempt.exam.subject'])
            ->latest('happened_at')
            ->paginate(20);

        return view('teacher.monitoring', [
            'violations' => $violations,
        ]);
    }

    public function destroyMonitoringViolation(ExamViolation $violation): RedirectResponse
    {
        $teacher = auth()->user();

        abort_unless($violation->attempt?->exam?->teacher_id === $teacher->id, 403);

        $attempt = $violation->attempt;
        $participantName = $attempt?->participantName() ?? 'peserta';

        $violation->delete();

        if ($attempt && $attempt->exists) {
            $attempt->refreshViolationCount();
        }

        return back()->with('status', 'Catatan monitoring untuk '.$participantName.' berhasil dihapus.');
    }

    public function destroyAllMonitoringViolations(): RedirectResponse
    {
        $teacher = auth()->user();

        $attempts = ExamAttempt::query()
            ->whereHas('exam', fn ($query) => $query->where('teacher_id', $teacher->id))
            ->get();

        $attemptIds = $attempts->pluck('id');

        if ($attemptIds->isNotEmpty()) {
            ExamViolation::query()
                ->whereIn('attempt_id', $attemptIds)
                ->delete();

            foreach ($attempts as $attempt) {
                $attempt->update(['violation_count' => 0]);
            }
        }

        return back()->with('status', 'Semua data monitoring pelanggaran berhasil dihapus.');
    }
}
