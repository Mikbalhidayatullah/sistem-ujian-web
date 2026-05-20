<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject_id',
        'teacher_id',
        'title',
        'description',
        'access_token',
        'access_pin',
        'start_at',
        'end_at',
        'duration_minutes',
        'max_violations',
        'is_active',
        'violations_enabled',
    ];

    protected function casts(): array
    {
        return [
            'start_at' => 'datetime',
            'end_at' => 'datetime',
            'is_active' => 'boolean',
            'violations_enabled' => 'boolean',
        ];
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function questions()
    {
        return $this->hasMany(Question::class)->orderBy('position');
    }

    public function attempts()
    {
        return $this->hasMany(ExamAttempt::class);
    }

    public function isOpenNow(): bool
    {
        return $this->isManuallyOpen() && $this->isWithinSchedule();
    }

    public function totalPoints(): int
    {
        return (int) $this->questions->sum('points');
    }

    public static function generateToken(): string
    {
        do {
            $token = Str::upper(Str::random(6));
        } while (self::query()->where('access_token', $token)->exists());

        return $token;
    }

    public static function generatePin(): string
    {
        return (string) random_int(100000, 999999);
    }

    public function isManuallyOpen(): bool
    {
        return $this->is_active;
    }

    public function isWithinSchedule(): bool
    {
        $now = now();

        if ($this->start_at instanceof Carbon && $now->lt($this->start_at)) {
            return false;
        }

        if ($this->end_at instanceof Carbon && $now->gt($this->end_at)) {
            return false;
        }

        return true;
    }
}
