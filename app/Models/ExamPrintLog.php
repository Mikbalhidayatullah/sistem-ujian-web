<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamPrintLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'exam_id',
        'printed_at',
        'channel',
    ];

    protected function casts(): array
    {
        return [
            'printed_at' => 'datetime',
        ];
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }
}
