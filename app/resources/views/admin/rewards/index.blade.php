@extends('adminlte::page')
@section('title','ของรางวัล')
@section('content_header')<h1>จัดการของรางวัล</h1>@stop
@section('content')
@if(session('success'))<div class="alert alert-success alert-dismissible"><button class="close" data-dismiss="alert">&times;</button>{{ session('success') }}</div>@endif

<div class="card">
    <div class="card-header">
        <div class="card-tools">
            <a href="{{ route('admin.rewards.create') }}" class="btn btn-sm btn-primary">
                <i class="fas fa-plus"></i> เพิ่มของรางวัล
            </a>
        </div>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover">
            <thead class="thead-light">
                <tr><th>รูป</th><th>ชื่อ</th><th>คะแนน</th><th>Training คะแนน</th><th>Stock</th><th>สถานะ</th><th></th></tr>
            </thead>
            <tbody>
                @forelse($rewards as $reward)
                <tr class="{{ !$reward->is_active ? 'table-secondary text-muted' : '' }}">
                    <td>
                        @if($reward->image)
                            <img src="{{ Storage::url($reward->image) }}" style="height:40px;width:40px;object-fit:cover;" class="img-thumbnail">
                        @else
                            <i class="fas fa-gift fa-2x text-muted"></i>
                        @endif
                    </td>
                    <td>{{ $reward->name }}</td>
                    <td><b>{{ number_format($reward->points_required) }}</b></td>
                    <td>{{ $reward->training_points_required > 0 ? number_format($reward->training_points_required) : '-' }}</td>
                    <td>
                        <span class="{{ $reward->stock <= 5 ? 'text-danger font-weight-bold' : '' }}">
                            {{ $reward->stock }}
                        </span>
                    </td>
                    <td>
                        @if($reward->is_active)
                            <span class="badge badge-success">เปิด</span>
                        @else
                            <span class="badge badge-secondary">ปิด</span>
                        @endif
                    </td>
                    <td class="text-nowrap">
                        <a href="{{ route('admin.rewards.edit', $reward) }}" class="btn btn-xs btn-warning">แก้ไข</a>
                        <form action="{{ route('admin.rewards.destroy', $reward) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('ลบของรางวัลนี้?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-xs btn-danger">ลบ</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted py-4">ยังไม่มีของรางวัล</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">{{ $rewards->links() }}</div>
</div>
@stop
