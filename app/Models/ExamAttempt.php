<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class ExamAttempt extends Model
{
    use HasFactory;

    public const STATUS_IN_PROGRESS = 'in_progress';

    public const STATUS_SUBMITTED = 'submitted';

    public const STATUS_AUTO_SUBMITTED = 'auto_submitted';

    protected $fillable = [
        'exam_id',
        'student_id',
        'student_name',
        'student_identifier',
        'question_order',
        'started_at',
        'submitted_at',
        'status',
        'score',
        'violation_count',
        'last_activity_at',
        'locked_at',
        'locked_reason',
        'submitted_reason',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'submitted_at' => 'datetime',
            'last_activity_at' => 'datetime',
            'locked_at' => 'datetime',
            'score' => 'decimal:2',
            'question_order' => 'array',
        ];
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function answers()
    {
        return $this->hasMany(ExamAnswer::class, 'attempt_id');
    }

    public function violations()
    {
        return $this->hasMany(ExamViolation::class, 'attempt_id')->latest('happened_at');
    }

    public function isSubmitted(): bool
    {
        return in_array($this->status, [self::STATUS_SUBMITTED, self::STATUS_AUTO_SUBMITTED], true);
    }

    public function isLocked(): bool
    {
        return $this->locked_at !== null && ! $this->isSubmitted();
    }

    public function unlockViolationLock(): void
    {
        $this->update([
            'locked_at' => null,
            'locked_reason' => null,
            'last_activity_at' => now(),
        ]);
    }

    public function expiresAt(): ?Carbon
    {
        if (! $this->started_at || ! $this->exam?->duration_minutes) {
            return null;
        }

        return $this->started_at->copy()->addMinutes($this->exam->duration_minutes);
    }

    public function isExpired(): bool
    {
        $expiresAt = $this->expiresAt();

        return $expiresAt instanceof Carbon ? now()->gte($expiresAt) : false;
    }

    public function participantName(): string
    {
        return $this->student_name ?: ($this->student?->name ?? 'Peserta');
    }

    public function finishedAt(): ?Carbon
    {
        return $this->submitted_at ?: ($this->isSubmitted() ? now() : null);
    }

    public function timeSpentInSeconds(): int
    {
        if (! $this->started_at) {
            return 0;
        }

        $finishedAt = $this->finishedAt() ?? now();

        return max(0, $this->started_at->diffInSeconds($finishedAt));
    }

    public function timeSpentForHumans(): string
    {
        $seconds = $this->timeSpentInSeconds();
        $hours = intdiv($seconds, 3600);
        $minutes = intdiv($seconds % 3600, 60);
        $remainingSeconds = $seconds % 60;

        $parts = [];

        if ($hours > 0) {
            $parts[] = $hours.' jam';
        }

        if ($minutes > 0) {
            $parts[] = $minutes.' menit';
        }

        if ($remainingSeconds > 0 || $parts === []) {
            $parts[] = $remainingSeconds.' detik';
        }

        return implode(' ', $parts);
    }

    public function answeredCount(): int
    {
        if ($this->relationLoaded('answers')) {
            return $this->answers->count();
        }

        return $this->answers()->count();
    }

    public function correctCount(): int
    {
        if ($this->relationLoaded('answers')) {
            return $this->answers->where('is_correct', true)->count();
        }

        return $this->answers()->where('is_correct', true)->count();
    }

    public function wrongCount(): int
    {
        if ($this->relationLoaded('answers')) {
            return $this->answers->where('is_correct', false)->count();
        }

        return $this->answers()->where('is_correct', false)->count();
    }

    public function refreshViolationCount(): void
    {
        $this->update([
            'violation_count' => $this->violations()->count(),
        ]);
    }
}
