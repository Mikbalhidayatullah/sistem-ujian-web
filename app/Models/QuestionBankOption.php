<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionBankOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_bank_id',
        'option_text',
        'is_correct',
        'position',
    ];

    protected function casts(): array
    {
        return [
            'is_correct' => 'boolean',
        ];
    }

    public function questionBank()
    {
        return $this->belongsTo(QuestionBank::class);
    }
}
