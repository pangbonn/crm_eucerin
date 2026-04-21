@extends('adminlte::page')

@section('title', 'จัดการสาขา')

@section('content_header')
    <h1>จัดการสาขา</h1>
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
                   placeholder="ชื่อสาขา" value="{{ request('search') }}">
            <select name="zone" class="form-control form-control-sm mr-2">
                <option value="">-- เขต --</option>
                @foreach($zones as $z)
                    <option value="{{ $z }}" {{ request('zone')==$z?'selected':'' }}>{{ $z }}</option>
                @endforeach
            </select>
            <select name="shop_type" class="form-control form-control-sm mr-2">
                <option value="">-- ประเภทร้าน --</option>
                @foreach($shopTypes as $t)
                    <option value="{{ $t }}" {{ request('shop_type')==$t?'selected':'' }}>{{ $t }}</option>
                @endforeach
            </select>
            <button class="btn btn-sm btn-primary mr-2">ค้นหา</button>
            <a href="{{ route('admin.branches.index') }}" class="btn btn-sm btn-secondary">รีเซ็ต</a>
        </form>
        <div class="card-tools">
            <a href="{{ route('admin.branches.create') }}" class="btn btn-sm btn-primary">
                <i class="fas fa-plus"></i> เพิ่มสาขา
            </a>
        </div>
    </div>

    <div class="card-body p-0">
        <table class="table table-sm table-hover">
            <thead class="thead-light">
                <tr><th>เขต</th><th>จังหวัด</th><th>ประเภทร้าน</th><th>ชื่อสาขา</th><th>สถานะ</th><th></th></tr>
            </thead>
            <tbody>
                @forelse($branches as $branch)
                <tr>
                    <td><span class="badge badge-secondary">{{ $branch->zone }}</span></td>
                    <td>{{ $branch->province ? $branch->province->name_th : '-' }}</td>
                    <td>{{ $branch->shop_type }}</td>
                    <td>{{ $branch->shop_name }}</td>
                    <td>
                        @if($branch->is_active)
                            <span class="badge badge-success">Active</span>
                        @else
                            <span class="badge badge-secondary">ปิด</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.branches.edit', $branch) }}" class="btn btn-xs btn-warning">แก้ไข</a>
                        <form action="{{ route('admin.branches.destroy', $branch) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('ลบสาขานี้?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-xs btn-danger">ลบ</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted py-4">ไม่พบข้อมูล</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">{{ $branches->links() }}</div>
</div>
@stop
