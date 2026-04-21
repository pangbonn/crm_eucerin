<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ExamPart;
use App\Models\ExamQuestion;
use App\Models\Point;
use App\Models\UserExamResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ExamController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $parts = ExamPart::where('is_active', true)
            ->orderByDesc('part_number')
            ->get();

        $myResults = UserExamResult::where('user_id', $user->id)
            ->get(['exam_part_id', 'section', 'passed'])
            ->groupBy('exam_part_id');

        return response()->json($parts->map(function ($p) use ($myResults) {
            $results = $myResults->get($p->id, collect());
            return [
                'id'          => $p->id,
                'name'        => $p->title,
                'part_number' => $p->part_number,
                'banner_image'=> $p->banner_image ? Storage::url($p->banner_image) : null,
                'vdo_url'     => $p->vdo_path ? Storage::url($p->vdo_path) : $p->vdo_url,
                'sections'    => ['pre', 'post'],
                'sections_count' => 2,
                'my_results'  => $results->map(function ($r) {
                    return ['section' => $r->section, 'passed' => (bool) $r->passed];
                })->values(),
            ];
        }));
    }

    public function questions(Request $request, ExamPart $exam)
    {
        $request->validate(['section' => 'required|in:pre,post']);
        $user = $request->user();

        // ตรวจว่าทำไปแล้วหรือยัง
        $done = UserExamResult::where('user_id', $user->id)
            ->where('exam_part_id', $exam->id)
            ->where('section', $request->section)
            ->where('passed', true)
            ->exists();

        if ($done) {
            return response()->json(['message' => 'ทำแบบทดสอบนี้ผ่านแล้ว'], 403);
        }

        $questions = ExamQuestion::where('exam_part_id', $exam->id)
            ->where('section', $request->section)
            ->orderBy('order')
            ->get(['id', 'question_text', 'choice_1', 'choice_2', 'choice_3', 'choice_4'])
            ->map(function ($q) {
                return [
                    'id'       => $q->id,
                    'question_text' => $q->question_text,
                    'choices'  => [$q->choice_1, $q->choice_2, $q->choice_3, $q->choice_4],
                ];
            });

        return response()->json($questions);
    }

    public function submit(Request $request, ExamPart $exam)
    {
        $request->validate([
            'section'  => 'required|in:pre,post',
            'answers'  => 'required|array',
            'answers.*'=> 'integer|min:0|max:3',
        ]);

        $user = $request->user();
        $section = $request->section;

        // ป้องกัน submit ซ้ำ
        $existing = UserExamResult::where('user_id', $user->id)
            ->where('exam_part_id', $exam->id)
            ->where('section', $section)
            ->first();

        if ($existing && $existing->passed) {
            return response()->json(['message' => 'ทำแบบทดสอบนี้ผ่านแล้ว'], 403);
        }

        $questions = ExamQuestion::where('exam_part_id', $exam->id)
            ->where('section', $section)
            ->orderBy('order')
            ->get();

        if ($questions->isEmpty()) {
            return response()->json(['message' => 'ไม่พบคำถาม'], 404);
        }

        $score = 0;
        foreach ($questions as $i => $q) {
            $answer = isset($request->answers[$i]) ? (int) $request->answers[$i] : -1;
            // correct_choice เป็น 1-4, answers เป็น 0-3
            if ($answer + 1 === $q->correct_choice) {
                $score++;
            }
        }

        $maxScore = $questions->count();
        $percentage = ($maxScore > 0) ? round($score / $maxScore * 100, 2) : 0;
        $passingScore = 70; // ต้องผ่าน 70%
        $passed = $percentage >= $passingScore;

        $pointsColumn = $section === 'pre' ? 'pre_test_points' : 'post_test_points';
        $earnedPoints = $passed ? (int) ($exam->{$pointsColumn} * ($percentage / 100)) : 0;

        // บันทึกผล (upsert ถ้าเคยสอบไม่ผ่าน)
        $result = UserExamResult::updateOrCreate(
            ['user_id' => $user->id, 'exam_part_id' => $exam->id, 'section' => $section],
            [
                'score'          => $score,
                'max_score'      => $maxScore,
                'percentage'     => $percentage,
                'passed'         => $passed,
                'points_awarded' => $earnedPoints,
                'completed_at'   => now(),
            ]
        );

        if ($passed && $earnedPoints > 0) {
            Point::create([
                'user_id'      => $user->id,
                'points'       => $earnedPoints,
                'source'       => $section === 'pre' ? 'exam_pre' : 'exam_post',
                'reference_id' => $result->id,
                'note'         => 'Exam Part ' . $exam->part_number . ' Section ' . strtoupper($section),
            ]);
        }

        return response()->json([
            'passed'        => $passed,
            'score'         => $score,
            'max_score'     => $maxScore,
            'percentage'    => $percentage,
            'passing_score' => $passingScore,
            'points_earned' => $earnedPoints,
        ]);
    }

    public function myResults(Request $request)
    {
        $user = $request->user();
        $results = UserExamResult::where('user_id', $user->id)->get();
        return response()->json($results);
    }
}
