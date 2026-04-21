<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamPart extends Model
{
    protected $fillable = [
        'title', 'part_number', 'banner_image', 'vdo_url', 'vdo_path',
        'pre_test_points', 'post_test_points', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function questions() { return $this->hasMany(ExamQuestion::class); }
    public function preQuestions()  { return $this->hasMany(ExamQuestion::class)->where('section', 'pre')->orderBy('order'); }
    public function postQuestions() { return $this->hasMany(ExamQuestion::class)->where('section', 'post')->orderBy('order'); }
    public function results()   { return $this->hasMany(UserExamResult::class); }
}
