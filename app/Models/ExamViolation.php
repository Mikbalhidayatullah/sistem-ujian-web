<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamViolation extends Model
{
    use HasFactory;

    protected $fillable = [
        'attempt_id',
        'violation_type',
        'detail',
        'happened_at',
    ];

    protected function casts(): array
    {
        return [
            'happened_at' => 'datetime',
        ];
    }

    public function attempt()
    {
        return $this->belongsTo(ExamAttempt::class, 'attempt_id');
    }
}
