<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\ExamAnswer;
use App\Models\ExamAttempt;
use App\Models\Exam;
use App\Models\Question;
use App\Models\ExamViolation;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ExamController extends Controller
{
    public function create(): View
    {
        $teacher = auth()->user();

        return view('teacher.exams.create', [
            'subjects' => $teacher->subjects()->latest()->get(),
            'exams' => $teacher->exams()
                ->with('subject')
                ->withCount(['questions', 'attempts'])
                ->latest()
                ->get(),
            'editingExam' => null,
        ]);
    }

    public function edit(Exam $exam): View
    {
        $teacher = auth()->user();

        abort_unless($exam->teacher_id === $teacher->id, 403);

        return view('teacher.exams.create', [
            'subjects' => $teacher->subjects()->latest()->get(),
            'exams' => $teacher->exams()
                ->with('subject')
                ->withCount(['questions', 'attempts'])
                ->latest()
                ->get(),
            'editingExam' => $exam->load('subject'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $teacher = $request->user();

        $data = $this->validateExamData($request, $teacher->id);

        $exam = Exam::create([
            'teacher_id' => $teacher->id,
            'subject_id' => $data['subject_id'],
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'access_token' => strtoupper($data['access_token'] ?? Exam::generateToken()),
            'access_pin' => $data['access_pin'] ?? Exam::generatePin(),
            'start_at' => $data['start_at'] ?? null,
            'end_at' => $data['end_at'] ?? null,
            'duration_minutes' => $data['duration_minutes'],
            'max_violations' => $data['max_violations'],
            'is_active' => $request->boolean('is_active'),
            'violations_enabled' => $request->boolean('violations_enabled', true),
        ]);

        return redirect()
            ->route('teacher.exams.show', $exam)
            ->with('status', 'Ujian berhasil dibuat. Sekarang tambahkan soal-soalnya.');
    }

    public function update(Request $request, Exam $exam): RedirectResponse
    {
        $teacher = $request->user();

        abort_unless($exam->teacher_id === $teacher->id, 403);

        $data = $this->validateExamData($request, $teacher->id, $exam);

        $exam->update([
            'subject_id' => $data['subject_id'],
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'access_token' => strtoupper($data['access_token'] ?? $exam->access_token),
            'access_pin' => $data['access_pin'] ?? $exam->access_pin,
            'start_at' => $data['start_at'] ?? null,
            'end_at' => $data['end_at'] ?? null,
            'duration_minutes' => $data['duration_minutes'],
            'max_violations' => $data['max_violations'],
            'is_active' => $request->boolean('is_active'),
            'violations_enabled' => $request->boolean('violations_enabled', true),
        ]);

        return redirect()
            ->route('teacher.exams.show', $exam)
            ->with('status', 'Ujian berhasil diperbarui.');
    }

    public function show(Request $request, Exam $exam): View
    {
        abort_unless($exam->teacher_id === auth()->id(), 403);

        $exam->load([
            'subject',
            'questions.options',
            'attempts.student',
            'attempts.answers.selectedOption',
        ]);

        $questionInsightSort = $this->normalizeQuestionInsightSort($request->query('insight_sort'));
        $questionInsights = $this->sortQuestionInsights(
            $this->buildQuestionInsights($exam),
            $questionInsightSort
        );
        $insightParticipants = $exam->attempts
            ->filter(fn ($attempt) => $attempt->isSubmitted() || $attempt->answers->isNotEmpty())
            ->count();

        $violations = ExamViolation::query()
            ->whereHas('attempt', fn ($query) => $query->where('exam_id', $exam->id))
            ->with('attempt.student')
            ->latest('happened_at')
            ->limit(5)
            ->get();

        return view('teacher.exams.show', [
            'exam' => $exam,
            'violations' => $violations,
            'questionInsights' => $questionInsights,
            'questionInsightSummary' => [
                'participants' => $insightParticipants,
                'most_correct' => $insightParticipants > 0 ? $questionInsights->sortByDesc('correct_percentage')->first() : null,
                'most_wrong' => $insightParticipants > 0 ? $questionInsights->sortByDesc('wrong_percentage')->first() : null,
            ],
            'questionInsightSort' => $questionInsightSort,
        ]);
    }

    public function destroy(Request $request, Exam $exam): RedirectResponse
    {
        abort_unless($exam->teacher_id === $request->user()->id, 403);

        DB::transaction(function () use ($exam): void {
            $mediaPaths = $exam->questions()
                ->whereNotNull('media_path')
                ->pluck('media_path')
                ->filter()
                ->unique()
                ->values();

            if ($mediaPaths->isNotEmpty()) {
                Storage::delete($mediaPaths->all());
            }

            $exam->delete();
        });

        return redirect()
            ->route('teacher.exams.create')
            ->with('status', 'Ujian berhasil dihapus.');
    }

    public function destroyAttempt(Request $request, Exam $exam, ExamAttempt $attempt): RedirectResponse
    {
        abort_unless($exam->teacher_id === $request->user()->id, 403);
        abort_unless($attempt->exam_id === $exam->id, 404);

        $participantName = $attempt->participantName();
        $attempt->delete();

        return back()->with('status', 'Data peserta '.$participantName.' berhasil dihapus.');
    }

    public function destroyAllAttempts(Request $request, Exam $exam): RedirectResponse
    {
        abort_unless($exam->teacher_id === $request->user()->id, 403);

        $deletedCount = $exam->attempts()->count();
        $exam->attempts()->delete();

        return back()->with('status', $deletedCount.' data peserta berhasil dihapus dari ujian ini.');
    }

    public function exportScores(Exam $exam): StreamedResponse
    {
        abort_unless($exam->teacher_id === auth()->id(), 403);

        $exam->load([
            'subject',
            'questions',
            'attempts.answers',
            'attempts.student',
        ]);

        $filename = 'rekap-nilai-'.str($exam->title)->slug('-').'.xls';

        return response()->streamDownload(function () use ($exam): void {
            echo "\xEF\xBB\xBF";
            echo view('teacher.exams.export', [
                'exam' => $exam,
            ])->render();
        }, $filename, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    public function toggleAccess(Request $request, Exam $exam): RedirectResponse
    {
        abort_unless($exam->teacher_id === $request->user()->id, 403);

        $data = $request->validate([
            'action' => ['required', Rule::in(['open', 'close'])],
        ]);

        $shouldOpen = $data['action'] === 'open';

        $exam->update([
            'is_active' => $shouldOpen,
        ]);

        return back()->with(
            'status',
            $shouldOpen
                ? 'Akses ujian dibuka secara manual. Siswa tetap harus sesuai jadwal jika tanggal ujian diisi.'
                : 'Akses ujian ditutup secara manual.'
        );
    }

    public function toggleViolations(Request $request, Exam $exam): RedirectResponse
    {
        abort_unless($exam->teacher_id === $request->user()->id, 403);

        $data = $request->validate([
            'action' => ['required', Rule::in(['enable', 'disable'])],
        ]);

        $shouldEnable = $data['action'] === 'enable';

        $exam->update([
            'violations_enabled' => $shouldEnable,
        ]);

        return back()->with(
            'status',
            $shouldEnable
                ? 'Pencatatan pelanggaran diaktifkan lagi untuk ujian ini.'
                : 'Pencatatan pelanggaran dimatikan. Mode ini cocok untuk ujian open book atau sesi yang lebih fleksibel.'
        );
    }

    public function destroyViolation(Request $request, Exam $exam, ExamViolation $violation): RedirectResponse
    {
        abort_unless($exam->teacher_id === $request->user()->id, 403);
        abort_unless($violation->attempt?->exam_id === $exam->id, 404);

        $attempt = $violation->attempt;
        $participantName = $attempt?->participantName() ?? 'peserta';

        $violation->delete();

        if ($attempt && $attempt->exists) {
            $attempt->refreshViolationCount();
        }

        return back()->with('status', 'Catatan pelanggaran untuk '.$participantName.' berhasil dihapus.');
    }

    public function destroyAllViolations(Request $request, Exam $exam): RedirectResponse
    {
        abort_unless($exam->teacher_id === $request->user()->id, 403);

        $attempts = $exam->attempts()->get();
        $attemptIds = $attempts->pluck('id');

        if ($attemptIds->isNotEmpty()) {
            ExamViolation::query()
                ->whereIn('attempt_id', $attemptIds)
                ->delete();

            foreach ($attempts as $attempt) {
                $attempt->update(['violation_count' => 0]);
            }
        }

        return back()->with('status', 'Semua log pelanggaran untuk ujian ini berhasil dihapus.');
    }

    public function importTemplate(Request $request, Exam $exam): RedirectResponse
    {
        abort_unless($exam->teacher_id === $request->user()->id, 403);

        $data = $request->validate([
            'question_template' => ['required', 'string'],
            'default_points' => ['required', 'integer', 'min:1', 'max:100'],
        ]);

        $questions = $this->parseQuestionTemplate(
            $data['question_template'],
            (int) $data['default_points']
        );

        $currentPosition = (int) ($exam->questions()->max('position') ?? 0);
        $createdCount = count($questions);

        DB::transaction(function () use ($exam, $questions, $currentPosition): void {
            foreach ($questions as $index => $questionData) {
                $question = $exam->questions()->create([
                    'prompt' => $questionData['prompt'],
                    'points' => $questionData['points'],
                    'position' => $currentPosition + $index + 1,
                ]);

                foreach ($questionData['options'] as $optionIndex => $optionText) {
                    $question->options()->create([
                        'option_text' => $optionText,
                        'is_correct' => $optionIndex === $questionData['correct_index'],
                        'position' => $optionIndex + 1,
                    ]);
                }
            }
        });

        return back()->with('status', $createdCount.' soal berhasil ditambahkan otomatis dari template.');
    }

    public function storeQuestion(Request $request, Exam $exam): RedirectResponse
    {
        abort_unless($exam->teacher_id === $request->user()->id, 403);

        $data = $request->validate([
            'prompt' => ['required', 'string'],
            'points' => ['required', 'integer', 'min:1', 'max:100'],
            'position' => ['nullable', 'integer', 'min:1'],
            'media' => ['nullable', 'file', 'mimetypes:image/jpeg,image/png,image/webp,video/mp4,video/webm,video/ogg', 'max:20480'],
            'options' => ['required', 'array', 'size:4'],
            'options.*' => ['required', 'string', 'max:255'],
            'correct_option' => ['required', 'integer', 'between:0,3'],
        ]);

        $mediaPath = null;
        $mediaType = null;

        if ($request->hasFile('media')) {
            $mediaPath = $request->file('media')->store('question-media');
            $mimeType = $request->file('media')->getMimeType();
            $mediaType = str_starts_with((string) $mimeType, 'video/') ? 'video' : 'image';
        }

        $question = $exam->questions()->create([
            'prompt' => $data['prompt'],
            'points' => $data['points'],
            'position' => $data['position'] ?? ($exam->questions()->count() + 1),
            'media_path' => $mediaPath,
            'media_type' => $mediaType,
        ]);

        foreach ($data['options'] as $index => $optionText) {
            $question->options()->create([
                'option_text' => $optionText,
                'is_correct' => $index === (int) $data['correct_option'],
                'position' => $index + 1,
            ]);
        }

        return back()->with('status', 'Soal berhasil ditambahkan.');
    }

    public function updateDefaultPoints(Request $request, Exam $exam): RedirectResponse
    {
        abort_unless($exam->teacher_id === $request->user()->id, 403);

        $data = $request->validate([
            'default_points' => ['required', 'integer', 'min:1', 'max:100'],
        ], [
            'default_points.required' => 'Poin default wajib diisi.',
            'default_points.integer' => 'Poin default harus berupa angka bulat.',
            'default_points.min' => 'Poin default minimal 1.',
            'default_points.max' => 'Poin default maksimal 100.',
        ]);

        $updatedPoints = (int) $data['default_points'];

        DB::transaction(function () use ($exam, $updatedPoints): void {
            $exam->questions()->update([
                'points' => $updatedPoints,
            ]);

            $this->refreshExamScores($exam->fresh('questions'));
        });

        return back()->with('status', 'Poin default bank soal berhasil diperbarui.');
    }

    public function destroyAllQuestions(Request $request, Exam $exam): RedirectResponse
    {
        abort_unless($exam->teacher_id === $request->user()->id, 403);

        DB::transaction(function () use ($exam): void {
            $questions = $exam->questions()->get();
            $questionIds = $questions->pluck('id');

            $mediaPaths = $questions
                ->pluck('media_path')
                ->filter()
                ->unique()
                ->values();

            if ($mediaPaths->isNotEmpty()) {
                Storage::delete($mediaPaths->all());
            }

            if ($questionIds->isNotEmpty()) {
                ExamAnswer::query()
                    ->whereIn('question_id', $questionIds)
                    ->delete();
            }

            foreach ($questions as $question) {
                $question->options()->delete();
                $question->delete();
            }

            $this->refreshExamScores($exam->fresh('questions'));
        });

        return back()->with('status', 'Semua soal pada bank soal berhasil dihapus.');
    }

    public function updateQuestion(Request $request, Exam $exam, Question $question): RedirectResponse
    {
        abort_unless($exam->teacher_id === $request->user()->id, 403);
        abort_unless($question->exam_id === $exam->id, 404);

        $data = $request->validate([
            'editing_question_id' => ['nullable', 'integer'],
            'prompt' => ['required', 'string'],
            'points' => ['required', 'integer', 'min:1', 'max:100'],
            'position' => ['required', 'integer', 'min:1'],
            'media' => ['nullable', 'file', 'mimetypes:image/jpeg,image/png,image/webp,video/mp4,video/webm,video/ogg', 'max:20480'],
            'remove_media' => ['nullable', 'boolean'],
            'options' => ['required', 'array', 'size:4'],
            'options.*' => ['required', 'string', 'max:255'],
            'correct_option' => ['required', 'integer', 'between:0,3'],
        ]);

        DB::transaction(function () use ($request, $exam, $question, $data): void {
            $mediaPath = $question->media_path;
            $mediaType = $question->media_type;

            if ($request->boolean('remove_media') && $question->media_path) {
                Storage::delete($question->media_path);
                $mediaPath = null;
                $mediaType = null;
            }

            if ($request->hasFile('media')) {
                if ($question->media_path) {
                    Storage::delete($question->media_path);
                }

                $uploadedMedia = $request->file('media');
                $mediaPath = $uploadedMedia->store('question-media');
                $mimeType = $uploadedMedia->getMimeType();
                $mediaType = str_starts_with((string) $mimeType, 'video/') ? 'video' : 'image';
            }

            $question->update([
                'prompt' => $data['prompt'],
                'points' => $data['points'],
                'media_path' => $mediaPath,
                'media_type' => $mediaType,
            ]);

            $question->loadMissing('options');

            foreach ($question->options->values() as $index => $option) {
                $option->update([
                    'option_text' => $data['options'][$index],
                    'is_correct' => $index === (int) $data['correct_option'],
                ]);
            }

            $this->reorderQuestionPosition($exam, $question, (int) $data['position']);
            $this->refreshExamScores($exam, $question->fresh('options'));
        });

        return back()->with('status', 'Soal berhasil diperbarui.');
    }

    public function destroyQuestion(Request $request, Exam $exam, Question $question): RedirectResponse
    {
        abort_unless($exam->teacher_id === $request->user()->id, 403);
        abort_unless($question->exam_id === $exam->id, 404);

        DB::transaction(function () use ($exam, $question): void {
            ExamAnswer::query()
                ->where('question_id', $question->id)
                ->delete();

            if ($question->media_path) {
                Storage::delete($question->media_path);
            }

            $question->options()->delete();
            $question->delete();

            $exam->questions()
                ->orderBy('position')
                ->get()
                ->values()
                ->each(function (Question $remainingQuestion, int $index): void {
                    $remainingQuestion->update([
                        'position' => $index + 1,
                    ]);
                });

            $this->refreshExamScores($exam->fresh('questions'));
        });

        return back()->with('status', 'Soal berhasil dihapus.');
    }

    private function parseQuestionTemplate(string $template, int $defaultPoints): array
    {
        $normalizedTemplate = trim(str_replace(["\r\n", "\r"], "\n", $template));

        if ($normalizedTemplate === '') {
            throw ValidationException::withMessages([
                'question_template' => 'Template soal tidak boleh kosong.',
            ]);
        }

        $blocks = preg_split('/\n\s*\n+/', $normalizedTemplate) ?: [];
        $questions = [];

        foreach ($blocks as $blockIndex => $block) {
            $lines = array_values(array_filter(
                array_map('trim', explode("\n", $block)),
                fn (string $line): bool => $line !== ''
            ));

            if ($lines === []) {
                continue;
            }

            $promptLines = [];
            $options = [];
            $correctLetter = null;
            $points = $defaultPoints;

            foreach ($lines as $line) {
                if (preg_match('/^(soal|pertanyaan)\s*[:\-]\s*(.+)$/iu', $line, $matches)) {
                    $promptLines[] = trim($matches[2]);
                    continue;
                }

                if (preg_match('/^\d+[\.\)]\s*(.+)$/u', $line, $matches) && $options === []) {
                    $promptLines[] = trim($matches[1]);
                    continue;
                }

                if (preg_match('/^([A-D])[\.\)]\s*(.+)$/iu', $line, $matches)) {
                    $options[] = trim($matches[2]);
                    continue;
                }

                if (preg_match('/^(jawaban|kunci)\s*[:\-]\s*([A-D])$/iu', $line, $matches)) {
                    $correctLetter = strtoupper($matches[2]);
                    continue;
                }

                if (preg_match('/^(poin|point|points)\s*[:\-]\s*(\d+)$/iu', $line, $matches)) {
                    $points = (int) $matches[2];
                    continue;
                }

                $promptLines[] = $line;
            }

            $questionNumber = $blockIndex + 1;
            $prompt = trim(implode("\n", $promptLines));

            if ($prompt === '') {
                throw ValidationException::withMessages([
                    'question_template' => 'Soal ke-'.$questionNumber.' belum memiliki pertanyaan.',
                ]);
            }

            if (count($options) !== 4) {
                throw ValidationException::withMessages([
                    'question_template' => 'Soal ke-'.$questionNumber.' harus memiliki tepat 4 opsi jawaban A sampai D.',
                ]);
            }

            if (! $correctLetter) {
                throw ValidationException::withMessages([
                    'question_template' => 'Soal ke-'.$questionNumber.' belum memiliki baris Jawaban atau Kunci.',
                ]);
            }

            $correctIndex = ord($correctLetter) - 65;

            if ($correctIndex < 0 || $correctIndex > 3) {
                throw ValidationException::withMessages([
                    'question_template' => 'Soal ke-'.$questionNumber.' memakai kunci jawaban yang tidak valid.',
                ]);
            }

            $questions[] = [
                'prompt' => $prompt,
                'points' => max(1, min(100, $points)),
                'options' => $options,
                'correct_index' => $correctIndex,
            ];
        }

        if ($questions === []) {
            throw ValidationException::withMessages([
                'question_template' => 'Template belum berisi soal yang bisa diproses.',
            ]);
        }

        return $questions;
    }

    private function buildQuestionInsights(Exam $exam)
    {
        $attempts = $exam->attempts
            ->filter(fn ($attempt) => $attempt->isSubmitted() || $attempt->answers->isNotEmpty())
            ->values();

        $participantCount = $attempts->count();

        return $exam->questions->map(function ($question) use ($attempts, $participantCount) {
            $responses = $attempts->map(function ($attempt) use ($question) {
                $answer = $attempt->answers->firstWhere('question_id', $question->id);
                $selectedOption = $answer?->selectedOption;

                return [
                    'student_name' => $attempt->participantName(),
                    'selected_option' => $selectedOption?->option_text,
                    'selected_option_position' => $selectedOption?->position,
                    'status' => $answer === null
                        ? 'Kosong'
                        : ($answer->is_correct ? 'Benar' : 'Salah'),
                    'is_correct' => $answer?->is_correct,
                    'points_awarded' => (float) ($answer?->points_awarded ?? 0),
                    'time_spent' => $attempt->timeSpentForHumans(),
                    'submit_status' => $attempt->status,
                ];
            })->values();

            $correctCount = $responses->where('is_correct', true)->count();
            $wrongCount = $responses->filter(fn ($response) => $response['is_correct'] === false)->count();
            $unansweredCount = $responses->filter(fn ($response) => $response['is_correct'] === null)->count();
            $correctOption = $question->options->firstWhere('is_correct', true);

            return collect([
                'id' => $question->id,
                'position' => $question->position,
                'prompt' => $question->prompt,
                'points' => $question->points,
                'correct_option' => $correctOption?->option_text,
                'correct_option_position' => $correctOption?->position,
                'responses' => $responses,
                'correct_count' => $correctCount,
                'wrong_count' => $wrongCount,
                'unanswered_count' => $unansweredCount,
                'correct_percentage' => $participantCount > 0 ? round(($correctCount / $participantCount) * 100, 1) : 0,
                'wrong_percentage' => $participantCount > 0 ? round(($wrongCount / $participantCount) * 100, 1) : 0,
                'unanswered_percentage' => $participantCount > 0 ? round(($unansweredCount / $participantCount) * 100, 1) : 0,
            ]);
        })->values();
    }

    private function normalizeQuestionInsightSort(mixed $sort): string
    {
        $allowedSorts = ['default', 'hardest', 'easiest', 'most_wrong', 'most_correct'];

        return in_array($sort, $allowedSorts, true) ? $sort : 'default';
    }

    private function sortQuestionInsights($questionInsights, string $sort)
    {
        return match ($sort) {
            'hardest' => $questionInsights
                ->sortByDesc(fn ($insight) => [
                    $insight['wrong_percentage'],
                    $insight['unanswered_percentage'],
                    -1 * $insight['position'],
                ])
                ->values(),
            'easiest' => $questionInsights
                ->sortByDesc(fn ($insight) => [
                    $insight['correct_percentage'],
                    -1 * $insight['wrong_percentage'],
                    -1 * $insight['position'],
                ])
                ->values(),
            'most_wrong' => $questionInsights
                ->sortByDesc(fn ($insight) => [
                    $insight['wrong_count'],
                    $insight['wrong_percentage'],
                    -1 * $insight['position'],
                ])
                ->values(),
            'most_correct' => $questionInsights
                ->sortByDesc(fn ($insight) => [
                    $insight['correct_count'],
                    $insight['correct_percentage'],
                    -1 * $insight['position'],
                ])
                ->values(),
            default => $questionInsights->sortBy('position')->values(),
        };
    }

    private function refreshExamScores(Exam $exam, ?Question $question = null): void
    {
        if ($question) {
            $question->loadMissing('options');
            $optionsById = $question->options->keyBy('id');

            ExamAnswer::query()
                ->where('question_id', $question->id)
                ->get()
                ->each(function (ExamAnswer $answer) use ($optionsById, $question): void {
                    $selectedOption = $optionsById->get($answer->question_option_id);
                    $isCorrect = (bool) ($selectedOption?->is_correct);

                    $answer->update([
                        'is_correct' => $isCorrect,
                        'points_awarded' => $isCorrect ? $question->points : 0,
                    ]);
                });
        }

        $exam->loadMissing('questions');
        $totalPoints = max(1, (int) $exam->questions->sum('points'));

        ExamAttempt::query()
            ->where('exam_id', $exam->id)
            ->get()
            ->each(function (ExamAttempt $attempt) use ($totalPoints): void {
                $earnedPoints = (float) $attempt->answers()->sum('points_awarded');
                $attempt->update([
                    'score' => round(($earnedPoints / $totalPoints) * 100, 2),
                ]);
            });
    }

    private function reorderQuestionPosition(Exam $exam, Question $question, int $newPosition): void
    {
        $orderedQuestions = $exam->questions()
            ->whereKeyNot($question->id)
            ->orderBy('position')
            ->get()
            ->values();

        $targetIndex = max(0, min($orderedQuestions->count(), $newPosition - 1));
        $orderedQuestions->splice($targetIndex, 0, [$question]);

        $orderedQuestions
            ->values()
            ->each(function (Question $orderedQuestion, int $index): void {
                if ($orderedQuestion->position !== $index + 1) {
                    $orderedQuestion->update([
                        'position' => $index + 1,
                    ]);
                }
            });
    }

    private function validateExamData(Request $request, int $teacherId, ?Exam $exam = null): array
    {
        return $request->validate([
            'subject_id' => [
                'required',
                Rule::exists('subjects', 'id')->where('teacher_id', $teacherId),
            ],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'access_token' => [
                'nullable',
                'string',
                'min:4',
                'max:20',
                'alpha_num',
                Rule::unique('exams', 'access_token')->ignore($exam?->id),
            ],
            'access_pin' => ['nullable', 'regex:/^\d{6}$/'],
            'start_at' => ['nullable', 'date'],
            'end_at' => ['nullable', 'date', 'after:start_at'],
            'duration_minutes' => ['required', 'integer', 'min:1', 'max:300'],
            'max_violations' => ['required', 'integer', 'min:1', 'max:20'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'subject_id.required' => 'Mata pelajaran wajib dipilih.',
            'subject_id.exists' => 'Mata pelajaran yang dipilih tidak valid.',
            'title.required' => 'Judul ujian wajib diisi.',
            'title.max' => 'Judul ujian maksimal 255 karakter.',
            'access_token.min' => 'Token ujian minimal 4 karakter.',
            'access_token.max' => 'Token ujian maksimal 20 karakter.',
            'access_token.alpha_num' => 'Token ujian hanya boleh berisi huruf dan angka tanpa spasi.',
            'access_token.unique' => 'Token ujian sudah dipakai. Gunakan token lain atau kosongkan untuk generate otomatis.',
            'access_pin.regex' => 'PIN ujian harus terdiri dari tepat 6 digit angka.',
            'end_at.after' => 'Waktu selesai harus setelah waktu mulai.',
            'duration_minutes.required' => 'Durasi ujian wajib diisi.',
            'duration_minutes.integer' => 'Durasi ujian harus berupa angka bulat.',
            'duration_minutes.min' => 'Durasi ujian minimal 1 menit.',
            'duration_minutes.max' => 'Durasi ujian maksimal 300 menit.',
            'max_violations.required' => 'Batas pelanggaran wajib diisi.',
            'max_violations.integer' => 'Batas pelanggaran harus berupa angka bulat.',
            'max_violations.min' => 'Batas pelanggaran minimal 1.',
            'max_violations.max' => 'Batas pelanggaran maksimal 20.',
        ], [
            'subject_id' => 'mata pelajaran',
            'title' => 'judul ujian',
            'description' => 'deskripsi',
            'access_token' => 'token ujian',
            'access_pin' => 'PIN ujian',
            'start_at' => 'waktu mulai',
            'end_at' => 'waktu selesai',
            'duration_minutes' => 'durasi ujian',
            'max_violations' => 'batas pelanggaran',
        ]);
    }
}
