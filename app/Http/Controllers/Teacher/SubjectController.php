<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index(Request $request)
    {
        $subjects = $request->user()
            ->subjects()
            ->withCount('exams')
            ->latest()
            ->get();

        $editingSubject = null;

        if ($request->filled('edit')) {
            $editingSubject = $request->user()
                ->subjects()
                ->whereKey($request->integer('edit'))
                ->first();
        }

        return view('teacher.subjects.index', [
            'subjects' => $subjects,
            'editingSubject' => $editingSubject,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'class_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        Subject::create([
            'teacher_id' => $request->user()->id,
            'name' => $data['name'],
            'class_name' => $data['class_name'],
            'description' => $data['description'] ?? null,
        ]);

        return redirect()
            ->route('teacher.subjects.index')
            ->with('status', 'Mata pelajaran berhasil ditambahkan.');
    }

    public function update(Request $request, Subject $subject): RedirectResponse
    {
        abort_unless($subject->teacher_id === $request->user()->id, 404);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'class_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $subject->update($data);

        return redirect()
            ->route('teacher.subjects.index')
            ->with('status', 'Mata pelajaran berhasil diperbarui.');
    }

    public function destroy(Request $request, Subject $subject): RedirectResponse
    {
        abort_unless($subject->teacher_id === $request->user()->id, 404);

        if ($subject->exams()->exists()) {
            return redirect()
                ->route('teacher.subjects.index')
                ->with('status', 'Mata pelajaran tidak bisa dihapus karena sudah dipakai pada ujian.');
        }

        $subject->delete();

        return redirect()
            ->route('teacher.subjects.index')
            ->with('status', 'Mata pelajaran berhasil dihapus.');
    }
}
