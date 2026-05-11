<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id',
        'prompt',
        'media_type',
        'media_path',
        'points',
        'position',
    ];

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function options()
    {
        return $this->hasMany(QuestionOption::class)->orderBy('position');
    }

    public function hasMedia(): bool
    {
        return filled($this->media_path);
    }
}
