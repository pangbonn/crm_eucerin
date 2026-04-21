@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Dashboard</h1>
@stop

@section('content')
<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $stats['users_active'] }}</h3>
                <p>พนักงาน Active</p>
            </div>
            <div class="icon"><i class="fas fa-users"></i></div>
            <a href="{{ route('admin.users.index') }}" class="small-box-footer">ดูทั้งหมด <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $stats['receipts_pending'] }}</h3>
                <p>ใบเสร็จรอตรวจสอบ</p>
            </div>
            <div class="icon"><i class="fas fa-receipt"></i></div>
            <a href="{{ route('admin.receipts.index') }}" class="small-box-footer">ดูทั้งหมด <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>{{ $stats['redemptions_pending'] }}</h3>
                <p>ของรางวัลรอการอนุมัติ</p>
            </div>
            <div class="icon"><i class="fas fa-gift"></i></div>
            <a href="{{ route('admin.redemptions.index') }}" class="small-box-footer">ดูทั้งหมด <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ number_format($stats['total_points']) }}</h3>
                <p>คะแนนสะสมรวม</p>
            </div>
            <div class="icon"><i class="fas fa-star"></i></div>
            <a href="{{ route('admin.points.index') }}" class="small-box-footer">ดูทั้งหมด <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">พนักงานสมัครใหม่ล่าสุด</h3>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm table-hover">
                    <thead>
                        <tr>
                            <th>ชื่อ-นามสกุล</th>
                            <th>รหัสพนักงาน</th>
                            <th>ระดับ</th>
                            <th>วันสมัคร</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentUsers as $user)
                        <tr>
                            <td>{{ $user->name }} {{ $user->lastname }}</td>
                            <td>{{ $user->employee_code }}</td>
                            <td>
                                <span class="badge badge-{{ $user->level === 'platinum' ? 'primary' : ($user->level === 'silver' ? 'secondary' : 'warning') }}">
                                    {{ ucfirst($user->level) }}
                                </span>
                            </td>
                            <td>{{ $user->created_at->format('d/m/Y') }}</td>
                            <td>
                                <a href="{{ route('admin.users.show', $user) }}" class="btn btn-xs btn-default">ดู</a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center text-muted">ยังไม่มีพนักงาน</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">ใบเสร็จรอตรวจสอบ</h3>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm table-hover">
                    <thead>
                        <tr>
                            <th>พนักงาน</th>
                            <th>วันที่ส่ง</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentReceipts as $receipt)
                        <tr>
                            <td>{{ $receipt->user->name ?? '-' }}</td>
                            <td>{{ $receipt->created_at->format('d/m/Y') }}</td>
                            <td>
                                <a href="{{ route('admin.receipts.show', $receipt) }}" class="btn btn-xs btn-warning">ตรวจสอบ</a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="text-center text-muted">ไม่มีรายการรอ</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card card-outline card-secondary">
            <div class="card-body">
                <p class="text-muted mb-1">พนักงานลาออก</p>
                <h4>{{ $stats['users_resigned'] }} คน</h4>
            </div>
        </div>
    </div>
</div>

<form id="logout-form" action="{{ route('admin.logout') }}" method="POST" class="d-none">@csrf</form>
@stop
