@extends('adminlte::page')

@section('title', 'ข้อมูลพนักงาน')

@section('content_header')
    <h1>ข้อมูลพนักงาน</h1>
@stop

@section('content')
@if(session('success'))
    <div class="alert alert-success alert-dismissible">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        {{ session('success') }}
    </div>
@endif

<div class="row">
    <div class="col-md-4">
        <div class="card card-primary card-outline">
            <div class="card-body box-profile">
                <div class="text-center mb-3">
                    <img src="{{ $user->photo_url ?? 'https://ui-avatars.com/api/?name='.$user->name.'&background=dc3545&color=fff&size=100' }}"
                         class="img-circle elevation-2" style="width:100px;height:100px;object-fit:cover;">
                </div>
                <h3 class="profile-username text-center">{{ $user->name }} {{ $user->lastname }}</h3>
                <p class="text-muted text-center">
                    @php $colors = ['platinum'=>'primary','silver'=>'secondary','gold'=>'warning'] @endphp
                    <span class="badge badge-{{ $colors[$user->level] ?? 'light' }}">{{ ucfirst($user->level) }}</span>
                    @if(!$user->is_active)
                        <span class="badge badge-danger ml-1">ลาออก</span>
                    @endif
                </p>
                <ul class="list-group list-group-unbordered mb-3">
                    <li class="list-group-item"><b>รหัสพนักงาน</b> <span class="float-right">{{ $user->employee_code }}</span></li>
                    <li class="list-group-item"><b>เบอร์โทร</b> <span class="float-right">{{ $user->phone }}</span></li>
                    <li class="list-group-item"><b>วันเกิด</b> <span class="float-right">{{ $user->birthdate ? $user->birthdate->format('d/m/Y') : '-' }}</span></li>
                    <li class="list-group-item"><b>ปีที่เข้างาน</b> <span class="float-right">{{ $user->start_year }}</span></li>
                    <li class="list-group-item"><b>คะแนนรวม</b> <span class="float-right text-primary"><b>{{ number_format($totalPoints) }}</b></span></li>
                    @if($user->currentBranch && $user->currentBranch->branch)
                    <li class="list-group-item"><b>เขต</b> <span class="float-right">{{ $user->currentBranch->branch->zone->name ?? '' }}</span></li>
                    <li class="list-group-item"><b>สาขา</b> <span class="float-right">{{ $user->currentBranch->branch->shop_name }}</span></li>
                    @endif
                </ul>

                <div class="d-flex gap-2">
                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-warning btn-block mr-2">
                        <i class="fas fa-edit"></i> แก้ไข
                    </a>
                    @if($user->is_active)
                    <form action="{{ route('admin.users.resign', $user) }}" method="POST"
                          onsubmit="return confirm('ยืนยันการลาออกของ {{ $user->name }}?')">
                        @csrf
                        <button class="btn btn-danger btn-block">
                            <i class="fas fa-user-times"></i> ลาออก
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        {{-- ประวัติคะแนน --}}
        <div class="card">
            <div class="card-header"><h3 class="card-title">ประวัติคะแนน (ล่าสุด 20 รายการ)</h3></div>
            <div class="card-body p-0">
                <table class="table table-sm table-hover">
                    <thead class="thead-light">
                        <tr><th>วันที่</th><th>แหล่งที่มา</th><th>คะแนน</th><th>หมายเหตุ</th></tr>
                    </thead>
                    <tbody>
                        @forelse($user->points as $point)
                        <tr>
                            <td><small>{{ $point->created_at->format('d/m/Y H:i') }}</small></td>
                            <td><span class="badge badge-info">{{ $point->source }}</span></td>
                            <td class="{{ $point->points >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ $point->points >= 0 ? '+' : '' }}{{ number_format($point->points) }}
                            </td>
                            <td><small>{{ $point->note }}</small></td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center text-muted">ยังไม่มีประวัติคะแนน</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ประวัติแลกรางวัล --}}
        <div class="card">
            <div class="card-header"><h3 class="card-title">ประวัติการแลกของรางวัล</h3></div>
            <div class="card-body p-0">
                <table class="table table-sm table-hover">
                    <thead class="thead-light">
                        <tr><th>วันที่</th><th>รางวัล</th><th>สถานะ</th></tr>
                    </thead>
                    <tbody>
                        @forelse($user->redemptions as $r)
                        <tr>
                            <td><small>{{ $r->created_at->format('d/m/Y') }}</small></td>
                            <td>{{ $r->reward ? $r->reward->name : '-' }}</td>
                            <td>
                                @php $badge = ['pending'=>'warning','approved'=>'success','rejected'=>'danger'] @endphp
                                <span class="badge badge-{{ $badge[$r->status] }}">{{ $r->status }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="text-center text-muted">ยังไม่มีการแลกรางวัล</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@stop
