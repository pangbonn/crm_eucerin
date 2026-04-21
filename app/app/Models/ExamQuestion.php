<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamQuestion extends Model
{
    protected $fillable = [
        'exam_part_id', 'section', 'question_text',
        'choice_1', 'choice_2', 'choice_3', 'choice_4',
        'correct_choice', 'order',
    ];

    protected $hidden = ['correct_choice']; // ไม่ส่งให้ frontend

    public function part() { return $this->belongsTo(ExamPart::class); }
}
