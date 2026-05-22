<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionBank extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'subject_id',
        'prompt',
        'media_type',
        'media_path',
        'points',
        'source_exam_title',
        'source_question_position',
    ];

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function options()
    {
        return $this->hasMany(QuestionBankOption::class)->orderBy('position');
    }

    public function hasMedia(): bool
    {
        return filled($this->media_path);
    }
}
