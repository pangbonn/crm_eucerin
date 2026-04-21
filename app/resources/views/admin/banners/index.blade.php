@extends('adminlte::page')
@section('title','จัดการ Banner')
@section('content_header')<h1>จัดการ Banner</h1>@stop
@section('content')
@if(session('success'))<div class="alert alert-success alert-dismissible"><button class="close" data-dismiss="alert">&times;</button>{{ session('success') }}</div>@endif

<div class="card-tools mb-3">
    <a href="{{ route('admin.banners.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> เพิ่ม Banner
    </a>
</div>

@foreach(['main'=>'หน้าหลัก','receipt'=>'ส่งใบเสร็จ','exam'=>'แบบทดสอบ','reward'=>'แลกรางวัล'] as $type=>$label)
<div class="card">
    <div class="card-header bg-light">
        <h3 class="card-title"><b>{{ $label }}</b> <small class="text-muted">({{ $type }})</small></h3>
    </div>
    <div class="card-body p-0">
        <table class="table table-sm">
            <thead class="thead-light">
                <tr><th>รูป</th><th>เดือน/ปี</th><th>เงื่อนไข</th><th>สถานะ</th><th></th></tr>
            </thead>
            <tbody>
                @forelse($banners[$type] ?? [] as $banner)
                <tr class="{{ !$banner->is_active ? 'table-secondary text-muted' : '' }}">
                    <td>
                        @if($banner->image_url)
                            <img src="{{ filter_var($banner->image_url, FILTER_VALIDATE_URL) ? $banner->image_url : Storage::url($banner->image_url) }}"
                                 style="height:50px;object-fit:cover;" class="img-thumbnail">
                        @endif
                    </td>
                    <td><small>{{ $banner->display_month ? $banner->display_month.'/'.$banner->display_year : 'ไม่กำหนด' }}</small></td>
                    <td><small>{{ Str::limit($banner->condition_text, 50) }}</small></td>
                    <td>
                        @if($banner->is_active)
                            <span class="badge badge-success">Active</span>
                        @else
                            <span class="badge badge-secondary">ปิด</span>
                        @endif
                    </td>
                    <td class="text-nowrap">
                        <a href="{{ route('admin.banners.edit', $banner) }}" class="btn btn-xs btn-warning">แก้ไข</a>
                        <form action="{{ route('admin.banners.destroy', $banner) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('ลบ Banner นี้?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-xs btn-danger">ลบ</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-muted py-2">ยังไม่มี Banner สำหรับ {{ $label }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endforeach
@stop
