@extends('adminlte::page')
@section('title', 'ตั้งค่าระบบ')
@section('content_header')<h1>ตั้งค่าระบบ</h1>@stop
@section('content')

@if(session('success'))
    <div class="alert alert-success alert-dismissible">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        {{ session('success') }}
    </div>
@endif

<div class="card" style="max-width:640px">
    <div class="card-body">
        <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- ชื่อระบบ --}}
            <div class="form-group">
                <label>ชื่อระบบ <span class="text-danger">*</span></label>
                <input type="text" name="site_name" class="form-control @error('site_name') is-invalid @enderror"
                       value="{{ old('site_name', $settings->get('site_name')->value ?? 'CRM Eucerin') }}" required>
                @error('site_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <small class="text-muted">แสดงใน Sidebar และ title ของหน้า</small>
            </div>

            {{-- โลโก้ --}}
            <div class="form-group">
                <label>โลโก้ระบบ</label>
                <div class="mb-2">
                    @php $logoVal = $settings->get('site_logo')->value ?? null; @endphp
                    @if($logoVal)
                        <img id="existing-logo" src="{{ Storage::url($logoVal) }}"
                             style="height:60px;object-fit:contain;" class="img-thumbnail">
                    @endif
                    <img id="preview-logo" src="#" alt="Preview" class="img-thumbnail"
                         style="height:60px;object-fit:contain;display:none;">
                </div>
                <input type="file" id="file-logo" name="site_logo" class="form-control-file" accept=".jpg,.jpeg,.png">
                <small class="text-muted">JPG/PNG ขนาดไม่เกิน 2MB — แสดงใน Sidebar และหน้า Login</small>
                @error('site_logo')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </div>

            <hr>

            {{-- สี Sidebar --}}
            <div class="form-group">
                <label>สี Sidebar <span class="text-danger">*</span></label>
                <select name="sidebar_theme" class="form-control">
                    @php
                    $sidebarOptions = [
                        'sidebar-dark-primary elevation-4'  => 'Dark — Primary (น้ำเงิน)',
                        'sidebar-dark-danger elevation-4'   => 'Dark — Danger (แดง)',
                        'sidebar-dark-success elevation-4'  => 'Dark — Success (เขียว)',
                        'sidebar-dark-warning elevation-4'  => 'Dark — Warning (ส้ม)',
                        'sidebar-dark-info elevation-4'     => 'Dark — Info (ฟ้า)',
                        'sidebar-light-primary elevation-4' => 'Light — Primary (น้ำเงิน)',
                        'sidebar-light-danger elevation-4'  => 'Light — Danger (แดง)',
                    ];
                    $currentSidebar = old('sidebar_theme', $settings->get('sidebar_theme')->value ?? 'sidebar-dark-primary elevation-4');
                    @endphp
                    @foreach($sidebarOptions as $val => $label)
                        <option value="{{ $val }}" {{ $currentSidebar === $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            {{-- สี Navbar --}}
            <div class="form-group">
                <label>สี Navbar (แถบบน) <span class="text-danger">*</span></label>
                <select name="navbar_theme" class="form-control">
                    @php
                    $navbarOptions = [
                        'navbar-white navbar-light'          => 'ขาว (Light)',
                        'navbar-dark bg-primary'             => 'Primary (น้ำเงิน)',
                        'navbar-dark bg-danger'              => 'Danger (แดง)',
                        'navbar-dark bg-success'             => 'Success (เขียว)',
                        'navbar-dark bg-warning'             => 'Warning (ส้ม)',
                        'navbar-dark bg-dark'                => 'Dark (ดำ)',
                    ];
                    $currentNavbar = old('navbar_theme', $settings->get('navbar_theme')->value ?? 'navbar-white navbar-light');
                    @endphp
                    @foreach($navbarOptions as $val => $label)
                        <option value="{{ $val }}" {{ $currentNavbar === $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            {{-- สีปุ่ม Login --}}
            <div class="form-group">
                <label>สีปุ่ม Login <span class="text-danger">*</span></label>
                <select name="auth_btn_class" class="form-control">
                    @php
                    $btnOptions = [
                        'btn-flat btn-danger'   => 'Danger (แดง)',
                        'btn-flat btn-primary'  => 'Primary (น้ำเงิน)',
                        'btn-flat btn-success'  => 'Success (เขียว)',
                        'btn-flat btn-warning'  => 'Warning (ส้ม)',
                        'btn-flat btn-dark'     => 'Dark (ดำ)',
                    ];
                    $currentBtn = old('auth_btn_class', $settings->get('auth_btn_class')->value ?? 'btn-flat btn-danger');
                    @endphp
                    @foreach($btnOptions as $val => $label)
                        <option value="{{ $val }}" {{ $currentBtn === $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                <small class="text-muted">สีของปุ่ม "เข้าสู่ระบบ" ในหน้า Login</small>
            </div>

            <hr>

            {{-- Stamp Card Settings --}}
            <h5 class="mb-3">การ์ดสะสม Stamp</h5>

            <div class="form-group">
                <label>จำนวน Stamp สูงสุด <span class="text-danger">*</span></label>
                <input type="number" name="stamp_max" min="1" max="100"
                       class="form-control @error('stamp_max') is-invalid @enderror"
                       value="{{ old('stamp_max', $settings->get('stamp_max')->value ?? 8) }}" required>
                <small class="text-muted">จำนวนช่อง Stamp บนการ์ด (เช่น 8 ช่อง)</small>
                @error('stamp_max')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label>คะแนนต่อ 1 Stamp <span class="text-danger">*</span></label>
                <input type="number" name="stamp_points" min="1"
                       class="form-control @error('stamp_points') is-invalid @enderror"
                       value="{{ old('stamp_points', $settings->get('stamp_points')->value ?? 10) }}" required>
                <small class="text-muted">ต้องสะสมกี่คะแนนถึงได้ 1 Stamp (เช่น 10 คะแนน = 1 Stamp)</small>
                @error('stamp_points')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <button type="submit" class="btn btn-primary">บันทึกการตั้งค่า</button>
        </form>
    </div>
</div>
@stop
@section('js')
<script>
document.getElementById('file-logo').addEventListener('change', function () {
    if (!this.files[0]) return;
    var prev = document.getElementById('preview-logo');
    prev.src = URL.createObjectURL(this.files[0]);
    prev.style.display = 'inline-block';
    var existing = document.getElementById('existing-logo');
    if (existing) existing.style.display = 'none';
});
</script>
@stop
