@extends('adminlte::page')
@section('title','แก้ไขคำถาม')
@section('content_header')<h1>แก้ไขคำถาม</h1>@stop
@section('content')
<div class="card" style="max-width:600px">
    <div class="card-body">
        <form action="{{ route('admin.questions.update', $question) }}" method="POST">
            @csrf @method('PUT')

            <div class="form-group">
                <label>คำถาม <span class="text-danger">*</span></label>
                <textarea name="question_text" class="form-control" rows="3" required>{{ old('question_text', $question->question_text) }}</textarea>
            </div>

            @foreach([1,2,3,4] as $n)
            <div class="form-group">
                <label>ตัวเลือก {{ $n }}</label>
                <input type="text" name="choice_{{ $n }}" class="form-control"
                       value="{{ old('choice_'.$n, $question->{'choice_'.$n}) }}" required>
            </div>
            @endforeach

            <div class="form-group">
                <label>คำตอบที่ถูก</label>
                <select name="correct_choice" class="form-control" required>
                    @foreach([1,2,3,4] as $n)
                        <option value="{{ $n }}" {{ old('correct_choice', $question->correct_choice)==$n?'selected':'' }}>ข้อ {{ $n }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>ลำดับ</label>
                <input type="number" name="order" class="form-control" value="{{ old('order', $question->order) }}">
            </div>

            <button type="submit" class="btn btn-primary">บันทึก</button>
            <a href="{{ route('admin.exams.questions.index', [$question->exam_part_id, 'section'=>$question->section]) }}"
               class="btn btn-secondary ml-2">ยกเลิก</a>
        </form>
    </div>
</div>
@stop
