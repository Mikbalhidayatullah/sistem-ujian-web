<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamAnswer;
use App\Models\ExamAttempt;
use App\Models\ExamViolation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AttemptController extends Controller
{
    public function status(Request $request): JsonResponse
    {
        $accessToken = Str::upper(trim((string) $request->query('access_token', '')));
        $accessPin = trim((string) $request->query('access_pin', ''));

        if ($accessToken === '' || $accessPin === '') {
            return response()->json([
                'found' => false,
                'can_start' => false,
                'state' => 'idle',
                'message' => 'Isi token dan PIN untuk memeriksa status ujian.',
            ]);
        }

        if (! preg_match('/^[A-Z0-9]{4,20}$/', $accessToken) || ! preg_match('/^\d{6}$/', $accessPin)) {
            return response()->json([
                'found' => false,
                'can_start' => false,
                'state' => 'invalid',
                'message' => 'Format token atau PIN belum sesuai.',
            ]);
        }

        $exam = Exam::query()
            ->with('subject')
            ->where('access_token', $accessToken)
            ->where('access_pin', $accessPin)
            ->first();

        if (! $exam) {
            return response()->json([
                'found' => false,
                'can_start' => false,
                'state' => 'not_found',
                'message' => 'Token atau PIN ujian tidak ditemukan.',
            ]);
        }

        if ($exam->isArchived()) {
            return response()->json([
                'found' => true,
                'can_start' => false,
                'state' => 'archived',
                'message' => 'Ujian ini sudah diarsipkan dan tidak menerima peserta baru.',
                'meta' => $exam->title.' | '.$exam->subject->display_name,
            ]);
        }

        if (! $exam->questions()->exists()) {
            return response()->json([
                'found' => true,
                'can_start' => false,
                'state' => 'empty',
                'message' => 'Ujian ditemukan, tetapi soal belum disiapkan guru.',
                'meta' => $exam->title.' | '.$exam->subject->display_name,
            ]);
        }

        if (! $exam->isManuallyOpen()) {
            return response()->json([
                'found' => true,
                'can_start' => false,
                'state' => 'closed',
                'message' => 'Akses ujian masih ditutup oleh guru.',
                'meta' => $exam->title.' | '.$exam->subject->display_name,
            ]);
        }

        if (! $exam->isWithinSchedule()) {
            return response()->json([
                'found' => true,
                'can_start' => false,
                'state' => 'scheduled',
                'message' => 'Ujian sudah terdaftar, tetapi belum masuk jadwal atau jadwalnya sudah selesai.',
                'meta' => $exam->title.' | '.$exam->subject->display_name,
            ]);
        }

        return response()->json([
            'found' => true,
            'can_start' => true,
            'state' => 'open',
            'message' => 'Ujian sedang dibuka. Anda bisa masuk sekarang.',
            'meta' => $exam->title.' | '.$exam->subject->display_name,
        ]);
    }

    public function start(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'student_identifier' => ['required', 'string', 'min:3', 'max:30', 'regex:/^[A-Za-z0-9\-]+$/'],
            'access_token' => ['required', 'string', 'min:4', 'max:20', 'alpha_num'],
            'access_pin' => ['required', 'string', 'size:6', 'digits:6'],
        ], [
            'student_identifier.required' => 'NIS, NISN, atau nomor absen wajib diisi.',
            'student_identifier.min' => 'Identitas siswa minimal 3 karakter.',
            'student_identifier.max' => 'Identitas siswa maksimal 30 karakter.',
            'student_identifier.regex' => 'Identitas siswa hanya boleh berisi huruf, angka, atau tanda hubung.',
        ]);

        $exam = Exam::query()
            ->where('access_token', Str::upper($data['access_token']))
            ->where('access_pin', $data['access_pin'])
            ->first();

        if (! $exam) {
            throw ValidationException::withMessages([
                'access_token' => 'Token atau PIN ujian tidak cocok.',
            ]);
        }

        if ($exam->isArchived()) {
            throw ValidationException::withMessages([
                'access_token' => 'Ujian ini sudah diarsipkan dan tidak menerima peserta baru.',
            ]);
        }

        abort_unless($exam->questions()->exists(), 422, 'Ujian belum memiliki soal.');
        abort_unless($exam->isOpenNow(), 422, 'Ujian belum dibuka atau sudah berakhir.');

        $attempt = $this->resolveOrCreateAttempt(
            $request,
            $exam,
            $data['full_name'],
            $this->normalizeStudentIdentifier($data['student_identifier'])
        );
        $this->grantSessionAccess($request, $attempt);

        return redirect()->route('exam.attempts.show', $attempt);
    }

    public function show(Request $request, ExamAttempt $attempt): View|RedirectResponse
    {
        $this->authorizeStudentAttempt($request, $attempt);

        $attempt->load([
            'exam.subject',
            'exam.teacher',
            'exam.questions.options',
            'answers',
        ]);

        if ($attempt->isSubmitted()) {
            return redirect()->route('exam.attempts.result', $attempt);
        }

        if ($attempt->isExpired()) {
            $this->finalizeAttempt($attempt, ExamAttempt::STATUS_AUTO_SUBMITTED, 'time_expired');

            return redirect()->route('exam.attempts.result', $attempt)->with('status', 'Waktu ujian habis. Jawaban tersimpan otomatis.');
        }

        $orderedQuestions = $this->orderedQuestionsForAttempt($attempt);

        return view('student.exams.take', [
            'attempt' => $attempt,
            'savedAnswers' => $attempt->answers->pluck('question_option_id', 'question_id'),
            'orderedQuestions' => $orderedQuestions,
        ]);
    }

    public function saveProgress(Request $request, ExamAttempt $attempt): JsonResponse
    {
        $this->authorizeStudentAttempt($request, $attempt);

        if ($attempt->isSubmitted()) {
            return response()->json(['saved' => false, 'submitted' => true], 422);
        }

        $answers = $this->validatedAnswers($request, $attempt);
        $this->syncAnswers($attempt, $answers);

        $attempt->update(['last_activity_at' => now()]);

        return response()->json(['saved' => true]);
    }

    public function submit(Request $request, ExamAttempt $attempt): RedirectResponse
    {
        $this->authorizeStudentAttempt($request, $attempt);

        if ($attempt->isSubmitted()) {
            return redirect()->route('exam.attempts.result', $attempt);
        }

        $answers = $this->validatedAnswers($request, $attempt);
        $this->syncAnswers($attempt, $answers);
        $this->finalizeAttempt($attempt, ExamAttempt::STATUS_SUBMITTED, 'submitted');

        return redirect()->route('exam.attempts.result', $attempt)->with('status', 'Jawaban berhasil dikumpulkan.');
    }

    public function result(Request $request, ExamAttempt $attempt): View
    {
        $this->authorizeStudentAttempt($request, $attempt);

        abort_unless($attempt->isSubmitted(), 404);

        $attempt->load([
            'exam.subject',
            'exam.teacher',
            'exam.questions.options',
            'answers.selectedOption',
        ]);

        $answers = $attempt->answers->keyBy('question_id');

        return view('student.exams.result', [
            'attempt' => $attempt,
            'answers' => $answers,
        ]);
    }

    public function recordViolation(Request $request, ExamAttempt $attempt): JsonResponse
    {
        $this->authorizeStudentAttempt($request, $attempt);
        $attempt->loadMissing('exam');

        if ($attempt->isSubmitted()) {
            return response()->json([
                'recorded' => false,
                'auto_submit' => false,
                'redirect_url' => route('exam.attempts.result', $attempt),
            ]);
        }

        if (! $attempt->exam->violations_enabled) {
            $attempt->update(['last_activity_at' => now()]);

            return response()->json([
                'recorded' => false,
                'auto_submit' => false,
                'disabled' => true,
                'redirect_url' => route('exam.attempts.result', $attempt),
            ]);
        }

        $data = $request->validate([
            'violation_type' => ['required', 'string', 'max:50'],
            'detail' => ['nullable', 'string', 'max:500'],
            'answers' => ['nullable', 'array'],
            'answers.*' => ['nullable', 'integer'],
        ]);

        if (! empty($data['answers'])) {
            $this->syncAnswers($attempt, $this->filterAnswersForExam($attempt, $data['answers']));
        }

        ExamViolation::create([
            'attempt_id' => $attempt->id,
            'violation_type' => $data['violation_type'],
            'detail' => $data['detail'] ?? null,
            'happened_at' => now(),
        ]);

        $attempt->increment('violation_count');
        $attempt->refresh();
        $attempt->update(['last_activity_at' => now()]);

        $mustAutoSubmit = $attempt->violation_count >= $attempt->exam->max_violations;

        if ($mustAutoSubmit) {
            $this->finalizeAttempt($attempt, ExamAttempt::STATUS_AUTO_SUBMITTED, 'max_violations');
        }

        return response()->json([
            'recorded' => true,
            'auto_submit' => $mustAutoSubmit,
            'violation_count' => $attempt->violation_count,
            'redirect_url' => route('exam.attempts.result', $attempt),
        ]);
    }

    protected function resolveOrCreateAttempt(Request $request, Exam $exam, string $fullName, string $studentIdentifier): ExamAttempt
    {
        $participantKey = $this->participantKey($exam->id, $studentIdentifier);
        $existingAttemptId = $request->session()->get("guest_attempts.$participantKey");

        if ($existingAttemptId) {
            $existingAttempt = ExamAttempt::query()
                ->whereKey($existingAttemptId)
                ->where('exam_id', $exam->id)
                ->first();

            if ($existingAttempt && ! $existingAttempt->isSubmitted()) {
                return $existingAttempt;
            }
        }

        $attemptByIdentifier = ExamAttempt::query()
            ->where('exam_id', $exam->id)
            ->where('student_identifier', $studentIdentifier)
            ->latest('id')
            ->first();

        if ($attemptByIdentifier) {
            $request->session()->put("guest_attempts.$participantKey", $attemptByIdentifier->id);

            if (! $attemptByIdentifier->isSubmitted()) {
                if ($attemptByIdentifier->student_name !== $fullName) {
                    $attemptByIdentifier->update([
                        'student_name' => $fullName,
                    ]);
                }

                return $this->ensureQuestionOrder($attemptByIdentifier->fresh());
            }

            throw ValidationException::withMessages([
                'student_identifier' => 'Identitas siswa ini sudah dipakai untuk menyelesaikan ujian dan tidak bisa masuk lagi.',
            ]);
        }

        $attempt = ExamAttempt::create([
            'exam_id' => $exam->id,
            'student_name' => $fullName,
            'student_identifier' => $studentIdentifier,
            'question_order' => $this->generateQuestionOrder($exam),
            'started_at' => now(),
            'last_activity_at' => now(),
            'status' => ExamAttempt::STATUS_IN_PROGRESS,
            'score' => 0,
            'violation_count' => 0,
        ]);

        $request->session()->put("guest_attempts.$participantKey", $attempt->id);

        return $attempt;
    }

    protected function grantSessionAccess(Request $request, ExamAttempt $attempt): void
    {
        $allowed = $request->session()->get('guest_allowed_attempts', []);
        $allowed[$attempt->id] = true;
        $request->session()->put('guest_allowed_attempts', $allowed);
    }

    protected function participantKey(int $examId, string $fullName): string
    {
        return md5($examId.'|'.Str::lower(trim($fullName)));
    }

    protected function normalizeStudentIdentifier(string $studentIdentifier): string
    {
        return Str::upper(trim($studentIdentifier));
    }

    protected function validatedAnswers(Request $request, ExamAttempt $attempt): array
    {
        $validated = $request->validate([
            'answers' => ['nullable', 'array'],
            'answers.*' => ['nullable', 'integer'],
        ]);

        return $this->filterAnswersForExam($attempt, $validated['answers'] ?? []);
    }

    protected function filterAnswersForExam(ExamAttempt $attempt, array $answers): array
    {
        $questionMap = $attempt->exam->questions()->with('options')->get()->keyBy('id');
        $filtered = [];

        foreach ($answers as $questionId => $optionId) {
            $question = $questionMap->get((int) $questionId);

            if (! $question) {
                continue;
            }

            $option = $question->options->firstWhere('id', (int) $optionId);

            if ($option) {
                $filtered[$question->id] = $option->id;
            }
        }

        return $filtered;
    }

    protected function syncAnswers(ExamAttempt $attempt, array $answers): void
    {
        $questions = $attempt->exam->questions()->with('options')->get()->keyBy('id');

        foreach ($questions as $question) {
            $optionId = $answers[$question->id] ?? null;

            if (! $optionId) {
                ExamAnswer::query()
                    ->where('attempt_id', $attempt->id)
                    ->where('question_id', $question->id)
                    ->delete();

                continue;
            }

            $selectedOption = $question->options->firstWhere('id', $optionId);

            if (! $selectedOption) {
                continue;
            }

            ExamAnswer::updateOrCreate(
                [
                    'attempt_id' => $attempt->id,
                    'question_id' => $question->id,
                ],
                [
                    'question_option_id' => $selectedOption->id,
                    'is_correct' => $selectedOption->is_correct,
                    'points_awarded' => $selectedOption->is_correct ? $question->points : 0,
                ]
            );
        }
    }

    protected function finalizeAttempt(ExamAttempt $attempt, string $status, string $reason): void
    {
        $attempt->loadMissing(['exam.questions', 'answers']);

        $totalPoints = max(1, (int) $attempt->exam->questions->sum('points'));
        $earnedPoints = (float) $attempt->answers()->sum('points_awarded');
        $score = round(($earnedPoints / $totalPoints) * 100, 2);

        $attempt->update([
            'submitted_at' => now(),
            'status' => $status,
            'score' => $score,
            'submitted_reason' => $reason,
            'last_activity_at' => now(),
        ]);
    }

    protected function authorizeStudentAttempt(Request $request, ExamAttempt $attempt): void
    {
        $allowed = $request->session()->get('guest_allowed_attempts', []);

        abort_unless(isset($allowed[$attempt->id]), 403);

        $attempt->loadMissing('exam');
    }

    protected function generateQuestionOrder(Exam $exam): ?array
    {
        if (! $exam->shuffle_questions_per_student) {
            return null;
        }

        return $exam->questions()
            ->pluck('questions.id')
            ->shuffle()
            ->values()
            ->all();
    }

    protected function ensureQuestionOrder(ExamAttempt $attempt): ExamAttempt
    {
        $attempt->loadMissing('exam');

        if (! $attempt->exam->shuffle_questions_per_student) {
            if (! empty($attempt->question_order)) {
                $attempt->update(['question_order' => null]);
            }

            return $attempt->fresh(['exam']);
        }

        $currentOrder = collect($attempt->question_order ?? []);
        $questionIds = $attempt->exam->questions()->pluck('questions.id');

        if ($currentOrder->isEmpty()) {
            $attempt->update([
                'question_order' => $questionIds->shuffle()->values()->all(),
            ]);

            return $attempt->fresh(['exam']);
        }

        $validQuestionIds = $questionIds->map(fn ($id) => (int) $id)->all();
        $normalizedOrder = $currentOrder
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => in_array($id, $validQuestionIds, true))
            ->values();

        $missingIds = collect($validQuestionIds)
            ->reject(fn ($id) => $normalizedOrder->contains($id))
            ->values();

        $finalOrder = $normalizedOrder->concat($missingIds)->values()->all();

        if ($finalOrder !== ($attempt->question_order ?? [])) {
            $attempt->update(['question_order' => $finalOrder]);

            return $attempt->fresh(['exam']);
        }

        return $attempt;
    }

    protected function orderedQuestionsForAttempt(ExamAttempt $attempt)
    {
        $attempt = $this->ensureQuestionOrder($attempt);

        $questions = $attempt->exam->questions;

        if (! $attempt->exam->shuffle_questions_per_student || empty($attempt->question_order)) {
            return $questions->sortBy('position')->values();
        }

        $orderMap = collect($attempt->question_order)
            ->values()
            ->flip();

        return $questions
            ->sortBy(fn ($question) => $orderMap->get($question->id, PHP_INT_MAX))
            ->values();
    }
}
