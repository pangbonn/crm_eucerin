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
        $user  = $request->user();
        $parts = ExamPart::where('is_active', true)->orderBy('part_number')->get();

        $results = UserExamResult::where('user_id', $user->id)
            ->whereIn('exam_part_id', $parts->pluck('id'))
            ->get()
            ->groupBy(function ($r) {
                return $r->exam_part_id . '_' . $r->section;
            });

        return response()->json($parts->map(function ($p) use ($results) {
            $pre  = $results->get($p->id . '_pre')  ? $results->get($p->id . '_pre')->first()  : null;
            $vdo  = $results->get($p->id . '_vdo')  ? $results->get($p->id . '_vdo')->first()  : null;
            $post = $results->get($p->id . '_post') ? $results->get($p->id . '_post')->first() : null;

            $vdoUrl = $p->vdo_path ? Storage::url($p->vdo_path) : $p->vdo_url;

            return [
                'id'           => $p->id,
                'name'         => $p->title,
                'part_number'  => $p->part_number,
                'banner_image' => $p->banner_image ? Storage::url($p->banner_image) : null,
                'pre'  => [
                    'points'     => $p->pre_test_points,
                    'passed'     => $pre  ? (bool)  $pre->passed     : false,
                    'percentage' => $pre  ? (float) $pre->percentage : 0,
                ],
                'vdo'  => [
                    'url'        => $vdoUrl,
                    'percentage' => $vdo  ? (float) $vdo->percentage : 0,
                    'passed'     => $vdo  ? (bool)  $vdo->passed     : false,
                ],
                'post' => [
                    'points'     => $p->post_test_points,
                    'passed'     => $post ? (bool)  $post->passed     : false,
                    'percentage' => $post ? (float) $post->percentage : 0,
                ],
            ];
        }));
    }

    public function videoProgress(Request $request, ExamPart $exam)
    {
        $request->validate(['percentage' => 'required|numeric|min:0|max:100']);
        $user       = $request->user();
        $percentage = (float) $request->percentage;
        $passed     = $percentage >= 100;

        UserExamResult::updateOrCreate(
            ['user_id' => $user->id, 'exam_part_id' => $exam->id, 'section' => 'vdo'],
            [
                'percentage'   => $percentage,
                'passed'       => $passed,
                'completed_at' => $passed ? now() : null,
            ]
        );

        return response()->json(['percentage' => $percentage, 'passed' => $passed]);
    }

    public function questions(Request $request, ExamPart $exam)
    {
        $request->validate(['section' => 'required|in:pre,post']);
        $user = $request->user();

        $done = UserExamResult::where('user_id', $user->id)
            ->where('exam_part_id', $exam->id)
            ->where('section', $request->section)
            ->where('passed', true)
            ->exists();

        if ($done) {
            return response()->json(['message' => 'ทำแบบทดสอบนี้ผ่านแล้ว'], 403);
        }

        $limit = $exam->questions_per_session ?? 10;

        $questions = ExamQuestion::where('exam_part_id', $exam->id)
            ->where('section', $request->section)
            ->inRandomOrder()
            ->take($limit)
            ->get(['id', 'question_text', 'choice_1', 'choice_2', 'choice_3', 'choice_4'])
            ->map(function ($q) {
                return [
                    'id'            => $q->id,
                    'question_text' => $q->question_text,
                    'choices'       => [$q->choice_1, $q->choice_2, $q->choice_3, $q->choice_4],
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

        $user    = $request->user();
        $section = $request->section;

        $existing = UserExamResult::where('user_id', $user->id)
            ->where('exam_part_id', $exam->id)
            ->where('section', $section)
            ->first();

        if ($existing && $existing->passed) {
            return response()->json(['message' => 'ทำแบบทดสอบนี้ผ่านแล้ว'], 403);
        }

        // answers เป็น {question_id: choice_index(0-3)}
        $questionIds = array_keys($request->answers);
        $questions   = ExamQuestion::where('exam_part_id', $exam->id)
            ->where('section', $section)
            ->whereIn('id', $questionIds)
            ->get();

        if ($questions->isEmpty()) {
            return response()->json(['message' => 'ไม่พบคำถาม'], 404);
        }

        $score = 0;
        foreach ($questions as $q) {
            $answer = isset($request->answers[$q->id]) ? (int) $request->answers[$q->id] : -1;
            if ($answer + 1 === $q->correct_choice) {
                $score++;
            }
        }

        $maxScore     = $questions->count();
        $percentage   = ($maxScore > 0) ? round($score / $maxScore * 100, 2) : 0;
        $passingScore = $section === 'pre' ? 10 : 70;
        $passed       = $percentage >= $passingScore;

        $pointsColumn = $section === 'pre' ? 'pre_test_points' : 'post_test_points';
        // คะแนน = สัดส่วนตามเปอร์เซ็นที่ทำได้จริง (ผ่านเกณฑ์เท่านั้นที่ได้คะแนน)
        $earnedPoints = $passed ? (int) round($exam->{$pointsColumn} * $percentage / 100) : 0;

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
