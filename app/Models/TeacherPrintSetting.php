<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeacherPrintSetting extends Model
{
    protected $fillable = [
        'teacher_id',
        'school_name',
        'school_department',
        'school_address',
        'logo_path',
    ];

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }
}
