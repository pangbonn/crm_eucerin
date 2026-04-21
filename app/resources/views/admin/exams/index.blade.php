@extends('adminlte::page')
@section('title','Exam & Training')
@section('content_header')<h1>Exam & Training</h1>@stop
@section('content')
@if(session('success'))<div class="alert alert-success alert-dismissible"><button class="close" data-dismiss="alert">&times;</button>{{ session('success') }}</div>@endif

<div class="card">
    <div class="card-header">
        <div class="card-tools">
            <a href="{{ route('admin.exams.create') }}" class="btn btn-sm btn-primary">
                <i class="fas fa-plus"></i> เพิ่ม Part ใหม่
            </a>
        </div>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover">
            <thead class="thead-light">
                <tr><th>Part</th><th>ชื่อ</th><th>VDO</th><th>คะแนน Pre/Post</th><th>คำถาม</th><th>ผู้ทำสอบ</th><th>สถานะ</th><th></th></tr>
            </thead>
            <tbody>
                @forelse($parts as $part)
                <tr>
                    <td><b>{{ $part->part_number }}</b></td>
                    <td>{{ $part->title }}</td>
                    <td>
                        @if($part->vdo_url)
                            <a href="{{ $part->vdo_url }}" target="_blank" class="btn btn-xs btn-secondary">
                                <i class="fas fa-play"></i> ดู VDO
                            </a>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td><small>Pre: {{ $part->pre_test_points }} / Post: {{ $part->post_test_points }}</small></td>
                    <td><small>{{ $part->questions_count }} ข้อ</small></td>
                    <td><small>{{ $part->results_count }} คน</small></td>
                    <td>
                        @if($part->is_active)
                            <span class="badge badge-success">เปิด</span>
                        @else
                            <span class="badge badge-secondary">ปิด</span>
                        @endif
                    </td>
                    <td class="text-nowrap">
                        <a href="{{ route('admin.exams.questions.index', $part) }}" class="btn btn-xs btn-info">คำถาม</a>
                        <a href="{{ route('admin.exams.results', $part) }}" class="btn btn-xs btn-default">ผลสอบ</a>
                        <a href="{{ route('admin.exams.edit', $part) }}" class="btn btn-xs btn-warning">แก้ไข</a>
                        <form action="{{ route('admin.exams.destroy', $part) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('ลบ Part นี้?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-xs btn-danger">ลบ</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center text-muted py-4">ยังไม่มี Part</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@stop
