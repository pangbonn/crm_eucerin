@extends('adminlte::page')
@section('title','รายงาน')
@section('content_header')<h1>รายงาน / Export</h1>@stop
@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header"><h3 class="card-title">Export ข้อมูล</h3></div>
            <div class="card-body">

                <div class="d-flex align-items-center justify-content-between border-bottom py-2">
                    <div>
                        <b>พนักงานทั้งหมด</b>
                        <p class="text-muted mb-0 small">ชื่อ, รหัส, ระดับ, คะแนน, วันสมัคร</p>
                    </div>
                    <a href="{{ route('admin.reports.export', 'users') }}" class="btn btn-success btn-sm">
                        <i class="fas fa-file-excel"></i> Export
                    </a>
                </div>

                <div class="d-flex align-items-center justify-content-between border-bottom py-2">
                    <div>
                        <b>คะแนนสะสม</b>
                        <p class="text-muted mb-0 small">คะแนนรวมรายพนักงาน</p>
                    </div>
                    <a href="{{ route('admin.reports.export', 'points') }}" class="btn btn-success btn-sm">
                        <i class="fas fa-file-excel"></i> Export
                    </a>
                </div>

                <div class="d-flex align-items-center justify-content-between border-bottom py-2">
                    <div>
                        <b>ยอดแลกรางวัล</b>
                        <p class="text-muted mb-0 small">รายการจัดส่งที่อนุมัติแล้ว</p>
                        <form method="GET" action="{{ route('admin.reports.export', 'redemptions') }}"
                              class="form-inline mt-1" id="redemption-export-form">
                            <select name="month" class="form-control form-control-sm mr-1">
                                <option value="">ทุกเดือน</option>
                                @foreach(range(1,12) as $m)
                                    <option value="{{ $m }}">เดือน {{ $m }}</option>
                                @endforeach
                            </select>
                            <select name="year" class="form-control form-control-sm mr-1">
                                <option value="">ทุกปี</option>
                                @foreach(range(now()->year, now()->year-2) as $y)
                                    <option value="{{ $y }}">{{ $y }}</option>
                                @endforeach
                            </select>
                        </form>
                    </div>
                    <button type="submit" form="redemption-export-form" class="btn btn-success btn-sm">
                        <i class="fas fa-file-excel"></i> Export
                    </button>
                </div>

                <div class="d-flex align-items-center justify-content-between py-2">
                    <div>
                        <b>ผลสอบ Exam</b>
                        <p class="text-muted mb-0 small">ผลสอบรายพนักงานทุก Part/Section</p>
                    </div>
                    <a href="{{ route('admin.reports.export', 'exam-results') }}" class="btn btn-success btn-sm">
                        <i class="fas fa-file-excel"></i> Export
                    </a>
                </div>

            </div>
        </div>
    </div>
</div>
@stop
