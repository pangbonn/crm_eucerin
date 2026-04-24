@extends('adminlte::page')
@section('title','จัดการคะแนน')
@section('content_header')<h1>จัดการคะแนน</h1>@stop
@section('content')
<div class="card">
    <div class="card-header">
        <form method="GET" class="form-inline">
            <input type="text" name="search" class="form-control form-control-sm mr-2"
                   placeholder="ชื่อ / รหัสพนักงาน" value="{{ request('search') }}">
            <button class="btn btn-sm btn-primary mr-2">ค้นหา</button>
            <a href="{{ route('admin.points.ranking') }}" class="btn btn-sm btn-info">
                <i class="fas fa-trophy"></i> Ranking
            </a>
        </form>
        <div class="card-tools">
            <a href="{{ route('admin.points.export') }}" class="btn btn-sm btn-success">
                <i class="fas fa-file-excel"></i> Export
            </a>
        </div>
    </div>
    <div class="card-body p-0">
        <table class="table table-sm table-hover">
            <thead class="thead-light">
                <tr><th>ชื่อ-นามสกุล</th><th>รหัส</th><th>ระดับ</th><th>คะแนนรวม</th><th></th></tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td>{{ $user->name }} {{ $user->lastname }}</td>
                    <td>{{ $user->employee_code }}</td>
                    <td>{{ ucfirst($user->level) }}</td>
                    <td><b class="text-primary">{{ number_format($user->accumulated_points ?? 0) }}</b></td>
                    <td><a href="{{ route('admin.points.show', $user) }}" class="btn btn-xs btn-default">ประวัติ / ปรับ</a></td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-muted py-3">ไม่มีข้อมูล</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">{{ $users->links() }}</div>
</div>
@stop
