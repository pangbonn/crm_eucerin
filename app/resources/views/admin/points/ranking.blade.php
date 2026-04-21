@extends('adminlte::page')
@section('title','Ranking')
@section('content_header')<h1>Ranking Top 10</h1>@stop
@section('content')
<div class="card" style="max-width:700px">
    <div class="card-header">
        <form method="GET" class="form-inline">
            <select name="month" class="form-control form-control-sm mr-2">
                @foreach(range(1,12) as $m)
                    <option value="{{ $m }}" {{ $month==$m?'selected':'' }}>เดือน {{ $m }}</option>
                @endforeach
            </select>
            <select name="year" class="form-control form-control-sm mr-2">
                @foreach(range(now()->year, now()->year-2) as $y)
                    <option value="{{ $y }}" {{ $year==$y?'selected':'' }}>{{ $y }}</option>
                @endforeach
            </select>
            <button class="btn btn-sm btn-primary">ดู</button>
        </form>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover">
            <thead class="thead-light"><tr><th>#</th><th>ชื่อ-นามสกุล</th><th>สาขา</th><th>ระดับ</th><th>คะแนน</th></tr></thead>
            <tbody>
                @forelse($rankings as $i => $r)
                @php
                    $ru = isset($r->user) ? $r->user : $r;
                    $rb = ($ru && $ru->currentBranch) ? $ru->currentBranch->branch : null;
                @endphp
                <tr class="{{ $i < 3 ? 'table-warning' : '' }}">
                    <td><b>{{ $r->rank ?? ($i+1) }}</b></td>
                    <td>{{ $ru->name ?? '-' }} {{ $ru->lastname ?? '' }}</td>
                    <td><small>{{ $rb ? $rb->shop_name : '-' }}</small></td>
                    <td>{{ ucfirst($ru->level ?? '-') }}</td>
                    <td><b class="text-primary">{{ number_format($r->total_points ?? $r->points_sum_points ?? 0) }}</b></td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-muted py-4">ไม่มีข้อมูล</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@stop
