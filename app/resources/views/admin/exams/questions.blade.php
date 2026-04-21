@extends('adminlte::page')
@section('title','คำถาม - '.$exam->title)
@section('content_header')<h1>คำถาม: {{ $exam->title }} (Part {{ $exam->part_number }})</h1>@stop
@section('content')
@if(session('success'))<div class="alert alert-success alert-dismissible"><button class="close" data-dismiss="alert">&times;</button>{{ session('success') }}</div>@endif

{{-- Tab Pre/Post --}}
<ul class="nav nav-tabs mb-3">
    <li class="nav-item">
        <a class="nav-link {{ $section==='pre'?'active':'' }}"
           href="{{ route('admin.exams.questions.index', [$exam, 'section'=>'pre']) }}">
           Pre-test ({{ $exam->preQuestions->count() }} ข้อ)
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $section==='post'?'active':'' }}"
           href="{{ route('admin.exams.questions.index', [$exam, 'section'=>'post']) }}">
           Post-test ({{ $exam->postQuestions->count() }} ข้อ)
        </a>
    </li>
</ul>

<div class="row">
    {{-- รายการคำถาม --}}
    <div class="col-md-7">
        <div class="card">
            <div class="card-header"><h3 class="card-title">คำถาม{{ $section==='pre'?'ก่อนเรียน':'หลังเรียน' }} ({{ $questions->count() }} ข้อ)</h3></div>
            <div class="card-body p-0">
                <table class="table table-sm">
                    <thead class="thead-light"><tr><th>#</th><th>คำถาม</th><th>เฉลย</th><th></th></tr></thead>
                    <tbody>
                        @forelse($questions as $q)
                        <tr>
                            <td>{{ $q->order }}</td>
                            <td><small>{{ Str::limit($q->question_text, 60) }}</small></td>
                            <td><span class="badge badge-success">ข้อ {{ $q->correct_choice }}</span></td>
                            <td class="text-nowrap">
                                <a href="{{ route('admin.questions.edit', $q) }}" class="btn btn-xs btn-warning">แก้ไข</a>
                                <form action="{{ route('admin.questions.destroy', $q) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('ลบคำถามนี้?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-xs btn-danger">ลบ</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center text-muted py-3">ยังไม่มีคำถาม</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ฟอร์มเพิ่มคำถาม --}}
    <div class="col-md-5">
        <div class="card card-primary card-outline">
            <div class="card-header"><h3 class="card-title">เพิ่มคำถามใหม่</h3></div>
            <div class="card-body">
                <form action="{{ route('admin.exams.questions.store', $exam) }}" method="POST">
                    @csrf
                    <input type="hidden" name="section" value="{{ $section }}">

                    <div class="form-group">
                        <label>คำถาม <span class="text-danger">*</span></label>
                        <textarea name="question_text" class="form-control @error('question_text') is-invalid @enderror"
                                  rows="3" required>{{ old('question_text') }}</textarea>
                        @error('question_text')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    @foreach([1,2,3,4] as $n)
                    <div class="form-group">
                        <label>ตัวเลือก {{ $n }} <span class="text-danger">*</span></label>
                        <input type="text" name="choice_{{ $n }}" class="form-control @error('choice_'.$n) is-invalid @enderror"
                               value="{{ old('choice_'.$n) }}" required>
                    </div>
                    @endforeach

                    <div class="form-group">
                        <label>คำตอบที่ถูก <span class="text-danger">*</span></label>
                        <select name="correct_choice" class="form-control" required>
                            @foreach([1,2,3,4] as $n)
                                <option value="{{ $n }}" {{ old('correct_choice')==$n?'selected':'' }}>ข้อ {{ $n }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>ลำดับ</label>
                        <input type="number" name="order" class="form-control" min="0" value="{{ old('order', $questions->count() + 1) }}">
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">เพิ่มคำถาม</button>
                </form>
            </div>
        </div>
    </div>
</div>

<a href="{{ route('admin.exams.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> กลับ</a>
@stop
