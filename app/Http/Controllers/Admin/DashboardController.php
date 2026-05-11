<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $teachers = User::query()
            ->where('role', User::ROLE_TEACHER)
            ->withCount(['subjects', 'exams'])
            ->latest()
            ->get();

        $totalTeachers = $teachers->count();
        $totalSubjects = Subject::query()->count();
        $totalExams = Exam::query()->count();
        $usedExams = Exam::query()->has('attempts')->count();
        $activeExams = Exam::query()
            ->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('start_at')
                    ->orWhere('start_at', '<=', now());
            })
            ->where(function ($query) {
                $query->whereNull('end_at')
                    ->orWhere('end_at', '>=', now());
            })
            ->count();
        $totalAttempts = ExamAttempt::query()->count();
        $submittedAttempts = ExamAttempt::query()
            ->whereIn('status', [
                ExamAttempt::STATUS_SUBMITTED,
                ExamAttempt::STATUS_AUTO_SUBMITTED,
            ])
            ->count();

        $teacherCharts = User::query()
            ->where('role', User::ROLE_TEACHER)
            ->withCount([
                'subjects',
                'exams',
                'exams as used_exams_count' => fn ($query) => $query->has('attempts'),
            ])
            ->orderByDesc('exams_count')
            ->take(6)
            ->get();

        $subjectCharts = Subject::query()
            ->with('teacher')
            ->withCount('exams')
            ->orderByDesc('exams_count')
            ->take(6)
            ->get();

        return view('admin.dashboard', [
            'teachers' => $teachers,
            'stats' => [
                'total_teachers' => $totalTeachers,
                'total_subjects' => $totalSubjects,
                'total_exams' => $totalExams,
                'used_exams' => $usedExams,
                'active_exams' => $activeExams,
                'unused_exams' => max($totalExams - $usedExams, 0),
                'total_attempts' => $totalAttempts,
                'submitted_attempts' => $submittedAttempts,
                'in_progress_attempts' => max($totalAttempts - $submittedAttempts, 0),
            ],
            'teacherCharts' => $teacherCharts,
            'subjectCharts' => $subjectCharts,
        ]);
    }

    public function accounts(Request $request): View
    {
        $teachers = User::query()
            ->where('role', User::ROLE_TEACHER)
            ->withCount([
                'subjects',
                'exams',
                'exams as used_exams_count' => fn ($query) => $query->has('attempts'),
            ])
            ->latest()
            ->get();

        $editingTeacher = null;

        if ($request->filled('edit')) {
            $editingTeacher = User::query()
                ->where('role', User::ROLE_TEACHER)
                ->whereKey($request->integer('edit'))
                ->withCount([
                    'subjects',
                    'exams',
                    'exams as used_exams_count' => fn ($query) => $query->has('attempts'),
                ])
                ->first();
        }

        return view('admin.accounts', [
            'teachers' => $teachers,
            'editingTeacher' => $editingTeacher,
        ]);
    }

    public function storeTeacher(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->mixedCase()->numbers()],
        ]);

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => User::ROLE_TEACHER,
            'password' => Hash::make($data['password']),
        ]);

        return back()->with('status', 'Akun guru berhasil dibuat.');
    }

    public function updateTeacher(Request $request, User $teacher): RedirectResponse
    {
        abort_unless($teacher->isTeacher(), 404);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($teacher->id),
            ],
            'password' => ['nullable', 'confirmed', Password::min(8)->letters()->mixedCase()->numbers()],
        ]);

        $teacher->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => filled($data['password'] ?? null)
                ? Hash::make($data['password'])
                : $teacher->password,
        ]);

        return redirect()
            ->route('admin.accounts.index')
            ->with('status', 'Akun guru berhasil diperbarui.');
    }
}
