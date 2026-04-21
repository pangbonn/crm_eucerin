<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExamPart;
use App\Models\ExamQuestion;
use Illuminate\Http\Request;

class ExamQuestionController extends Controller
{
    public function index(ExamPart $exam, Request $request)
    {
        $section   = $request->get('section', 'pre');
        $questions = $exam->questions()->where('section', $section)->orderBy('order')->get();
        return view('admin.exams.questions', compact('exam', 'questions', 'section'));
    }

    public function store(ExamPart $exam, Request $request)
    {
        $data = $request->validate([
            'section'        => 'required|in:pre,post',
            'question_text'  => 'required|string',
            'choice_1'       => 'required|string|max:300',
            'choice_2'       => 'required|string|max:300',
            'choice_3'       => 'required|string|max:300',
            'choice_4'       => 'required|string|max:300',
            'correct_choice' => 'required|integer|between:1,4',
            'order'          => 'integer|min:0',
        ]);

        $data['exam_part_id'] = $exam->id;
        $data['order'] = $data['order'] ?? ($exam->questions()->where('section', $data['section'])->max('order') + 1);

        ExamQuestion::create($data);

        return redirect()->route('admin.exams.questions.index', [$exam, 'section' => $data['section']])
                         ->with('success', 'เพิ่มคำถามเรียบร้อย');
    }

    public function edit(ExamQuestion $question)
    {
        return view('admin.exams.question-form', ['question' => $question, 'part' => $question->part]);
    }

    public function update(Request $request, ExamQuestion $question)
    {
        $data = $request->validate([
            'question_text'  => 'required|string',
            'choice_1'       => 'required|string|max:300',
            'choice_2'       => 'required|string|max:300',
            'choice_3'       => 'required|string|max:300',
            'choice_4'       => 'required|string|max:300',
            'correct_choice' => 'required|integer|between:1,4',
            'order'          => 'integer|min:0',
        ]);

        $question->update($data);

        return redirect()->route('admin.exams.questions.index', [$question->exam_part_id, 'section' => $question->section])
                         ->with('success', 'อัพเดทคำถามเรียบร้อย');
    }

    public function destroy(ExamQuestion $question)
    {
        $partId  = $question->exam_part_id;
        $section = $question->section;
        $question->delete();
        return redirect()->route('admin.exams.questions.index', [$partId, 'section' => $section])
                         ->with('success', 'ลบคำถามเรียบร้อย');
    }
}
