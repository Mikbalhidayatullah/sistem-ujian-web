<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'name',
        'class_name',
        'description',
    ];

    protected $appends = [
        'display_name',
    ];

    public function getDisplayNameAttribute(): string
    {
        return filled($this->class_name)
            ? $this->name.' | '.$this->class_name
            : $this->name;
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function exams()
    {
        return $this->hasMany(Exam::class);
    }

    public function questionBanks()
    {
        return $this->hasMany(QuestionBank::class);
    }
}
