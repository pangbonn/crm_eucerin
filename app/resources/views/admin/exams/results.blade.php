@extends('adminlte::page')
@section('title','ผลสอบ - '.$exam->title)
@section('content_header')<h1>ผลสอบ: {{ $exam->title }}</h1>@stop
@section('content')
<div class="card">
    <div class="card-header">
        <form method="GET" class="form-inline">
            <select name="section" class="form-control form-control-sm mr-2">
                <option value="">-- ทุก Section --</option>
                <option value="pre"  {{ request('section')==='pre'?'selected':'' }}>Pre-test</option>
                <option value="vdo"  {{ request('section')==='vdo'?'selected':'' }}>VDO</option>
                <option value="post" {{ request('section')==='post'?'selected':'' }}>Post-test</option>
            </select>
            <button class="btn btn-sm btn-primary">กรอง</button>
        </form>
    </div>
    <div class="card-body p-0">
        <table class="table table-sm table-hover">
            <thead class="thead-light">
                <tr><th>ชื่อ-นามสกุล</th><th>รหัส</th><th>Section</th><th>คะแนน</th><th>%</th><th>Stamp</th><th>วันที่</th></tr>
            </thead>
            <tbody>
                @forelse($results as $r)
                <tr>
                    <td>{{ $r->user ? $r->user->name.' '.$r->user->lastname : '-' }}</td>
                    <td><small>{{ $r->user ? $r->user->employee_code : '-' }}</small></td>
                    <td><span class="badge badge-info">{{ strtoupper($r->section) }}</span></td>
                    <td>{{ $r->score }}/{{ $r->max_score }}</td>
                    <td>{{ number_format($r->percentage,1) }}%</td>
                    <td>{{ $r->stamp_earned ? '<span class="badge badge-warning">ได้ Stamp</span>' : '-' }}</td>
                    <td><small>{{ $r->completed_at ? $r->completed_at->format('d/m/Y H:i') : '-' }}</small></td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted py-4">ยังไม่มีผลสอบ</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">{{ $results->links() }}</div>
</div>
<a href="{{ route('admin.exams.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> กลับ</a>
@stop
