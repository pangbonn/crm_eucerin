<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserExamResult extends Model
{
    protected $fillable = [
        'user_id', 'exam_part_id', 'section',
        'score', 'max_score', 'percentage',
        'stamp_earned', 'passed', 'points_awarded', 'completed_at',
    ];

    protected $casts = [
        'stamp_earned' => 'boolean',
        'passed'       => 'boolean',
        'completed_at' => 'datetime',
        'percentage'   => 'float',
    ];

    public function user()     { return $this->belongsTo(User::class); }
    public function examPart() { return $this->belongsTo(ExamPart::class); }
}
