@extends('adminlte::page')

@section('title', 'จัดการพนักงาน')

@section('content_header')
    <h1>จัดการพนักงาน</h1>
@stop

@section('content')
@if(session('success'))
    <div class="alert alert-success alert-dismissible">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        {{ session('success') }}
    </div>
@endif

<div class="card">
    <div class="card-header">
        <form method="GET" class="form-inline flex-wrap gap-2">
            <input type="text" name="search" class="form-control form-control-sm mr-2"
                   placeholder="ชื่อ / รหัส / เบอร์" value="{{ request('search') }}">
            <select name="level" class="form-control form-control-sm mr-2">
                <option value="">-- ระดับ --</option>
                <option value="gold"     {{ request('level')=='gold'?'selected':'' }}>Gold</option>
                <option value="silver"   {{ request('level')=='silver'?'selected':'' }}>Silver</option>
                <option value="platinum" {{ request('level')=='platinum'?'selected':'' }}>Platinum</option>
            </select>
            <select name="status" class="form-control form-control-sm mr-2">
                <option value="">-- สถานะ --</option>
                <option value="active"   {{ request('status')=='active'?'selected':'' }}>Active</option>
                <option value="resigned" {{ request('status')=='resigned'?'selected':'' }}>ลาออก</option>
            </select>
            <select name="zone" class="form-control form-control-sm mr-2">
                <option value="">-- เขต --</option>
                @foreach($zones as $z)
                    <option value="{{ $z }}" {{ request('zone')==$z?'selected':'' }}>{{ $z }}</option>
                @endforeach
            </select>
            <button class="btn btn-sm btn-primary mr-2">ค้นหา</button>
            <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-secondary">รีเซ็ต</a>
        </form>

        <div class="card-tools mt-2">
            <a href="{{ route('admin.users.export') }}" class="btn btn-sm btn-success">
                <i class="fas fa-file-excel"></i> Export Excel
            </a>
            <button class="btn btn-sm btn-info" data-toggle="modal" data-target="#importModal">
                <i class="fas fa-upload"></i> Import ระดับ
            </button>
        </div>
    </div>

    <div class="card-body p-0">
        <table class="table table-hover table-sm">
            <thead class="thead-light">
                <tr>
                    <th>ชื่อ-นามสกุล</th>
                    <th>รหัสพนักงาน</th>
                    <th>เบอร์โทร</th>
                    <th>ระดับ</th>
                    <th>เขต / สาขา</th>
                    <th>สถานะ</th>
                    <th>วันสมัคร</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr class="{{ !$user->is_active ? 'table-secondary text-muted' : '' }}">
                    <td>{{ $user->name }} {{ $user->lastname }}</td>
                    <td>{{ $user->employee_code }}</td>
                    <td>{{ $user->phone }}</td>
                    <td>
                        @php $colors = ['platinum'=>'primary','silver'=>'secondary','gold'=>'warning'] @endphp
                        <span class="badge badge-{{ $colors[$user->level] ?? 'light' }}">{{ ucfirst($user->level) }}</span>
                    </td>
                    <td>
                        @if($user->currentBranch && $user->currentBranch->branch)
                            <small>{{ $user->currentBranch->branch->zone }} / {{ $user->currentBranch->branch->shop_name }}</small>
                        @else
                            <small class="text-muted">-</small>
                        @endif
                    </td>
                    <td>
                        @if($user->is_active)
                            <span class="badge badge-success">Active</span>
                        @else
                            <span class="badge badge-danger">ลาออก</span>
                        @endif
                    </td>
                    <td><small>{{ $user->created_at->format('d/m/Y') }}</small></td>
                    <td>
                        <a href="{{ route('admin.users.show', $user) }}" class="btn btn-xs btn-default">ดู</a>
                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-xs btn-warning">แก้ไข</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center text-muted py-4">ไม่พบข้อมูล</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        {{ $users->links() }}
    </div>
</div>

{{-- Import Modal --}}
<div class="modal fade" id="importModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.users.import-level') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Import ระดับพนักงาน</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small">ไฟล์ Excel ต้องมี 2 columns: <code>employee_code</code>, <code>level</code> (gold/silver/platinum)</p>
                    <div class="form-group">
                        <input type="file" name="file" class="form-control-file" accept=".xlsx,.xls,.csv" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-primary">Import</button>
                </div>
            </div>
        </form>
    </div>
</div>
@stop
