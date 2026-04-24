@extends('adminlte::page')
@section('title', $part->exists ? 'แก้ไข Part' : 'เพิ่ม Part')
@section('content_header')<h1>{{ $part->exists ? 'แก้ไข Part: '.$part->title : 'เพิ่ม Part ใหม่' }}</h1>@stop
@section('content')
<div class="card" style="max-width:650px">
    <div class="card-body">
        <form action="{{ $part->exists ? route('admin.exams.update',$part) : route('admin.exams.store') }}"
              method="POST" enctype="multipart/form-data">
            @csrf
            @if($part->exists) @method('PUT') @endif

            <div class="row">
                <div class="col-md-8">
                    <div class="form-group">
                        <label>ชื่อ Part <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                               value="{{ old('title', $part->title) }}" required>
                        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Part Number <span class="text-danger">*</span></label>
                        <input type="number" name="part_number" class="form-control" min="1"
                               value="{{ old('part_number', $part->part_number ?? 1) }}" required>
                    </div>
                </div>
            </div>

            {{-- VDO Section --}}
            <div class="form-group">
                <label>VDO Training</label>
                <div class="card card-body bg-light p-3">
                    <p class="text-muted small mb-2">เลือกอย่างใดอย่างหนึ่ง: URL หรืออัพโหลดไฟล์</p>

                    <div class="form-group mb-2">
                        <label class="text-sm font-weight-normal">VDO URL (YouTube, Vimeo, ฯลฯ)</label>
                        <input type="url" name="vdo_url" class="form-control form-control-sm @error('vdo_url') is-invalid @enderror"
                               value="{{ old('vdo_url', $part->vdo_url) }}" placeholder="https://...">
                        @error('vdo_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group mb-0">
                        <label class="text-sm font-weight-normal">หรืออัพโหลดไฟล์ VDO (MP4/WebM/MOV, ไม่เกิน 10 MB)</label>
                        <div class="mb-2">
                            @if($part->exists && $part->vdo_path)
                                <video id="existing-vdo" src="{{ Storage::url($part->vdo_path) }}" controls style="max-height:120px;max-width:100%;" class="rounded"></video>
                                <p class="text-muted small mt-1">ไฟล์ปัจจุบัน: {{ basename($part->vdo_path) }}</p>
                            @endif
                            <video id="preview-vdo" controls style="max-height:120px;max-width:100%;display:none;" class="rounded"></video>
                        </div>
                        <input type="file" id="file-vdo" name="vdo_file" class="form-control-file @error('vdo_file') is-invalid @enderror"
                               accept=".mp4,.webm,.mov">
                        @error('vdo_file')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        <small class="text-muted">อัพโหลดแล้วจะใช้ไฟล์นี้แทน URL</small>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>คะแนน Pre-test</label>
                        <input type="number" name="pre_test_points" class="form-control" min="0"
                               value="{{ old('pre_test_points', $part->pre_test_points ?? 10) }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>คะแนน Post-test</label>
                        <input type="number" name="post_test_points" class="form-control" min="0"
                               value="{{ old('post_test_points', $part->post_test_points ?? 10) }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>จำนวนคำถาม/ครั้ง</label>
                        <input type="number" name="questions_per_session" class="form-control" min="1"
                               value="{{ old('questions_per_session', $part->questions_per_session ?? 10) }}">
                        <small class="text-muted">สุ่มจาก bank คำถามทั้งหมด</small>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>Banner Image</label>
                <div class="mb-2">
                    @if($part->exists && $part->banner_image)
                        <img id="existing-banner" src="{{ Storage::url($part->banner_image) }}" style="height:100px;object-fit:cover;" class="img-thumbnail">
                    @endif
                    <img id="preview-banner" src="#" alt="Preview" class="img-thumbnail" style="height:100px;object-fit:cover;display:none;">
                </div>
                <input type="file" id="file-banner" name="banner_image" class="form-control-file" accept=".jpg,.jpeg,.png">
                <small class="text-muted">JPG/PNG ขนาดไม่เกิน 2MB</small>
            </div>

            <div class="form-group">
                <div class="custom-control custom-switch">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1"
                           {{ old('is_active', $part->is_active ?? true) ? 'checked' : '' }}>
                    <label class="custom-control-label" for="is_active">เปิดใช้งาน</label>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">บันทึก</button>
            <a href="{{ route('admin.exams.index') }}" class="btn btn-secondary ml-2">ยกเลิก</a>
        </form>
    </div>
</div>
@stop
@section('js')
<script>
document.getElementById('file-vdo').addEventListener('change', function () {
    if (!this.files[0]) return;
    var prev = document.getElementById('preview-vdo');
    prev.src = URL.createObjectURL(this.files[0]);
    prev.style.display = 'block';
    var existing = document.getElementById('existing-vdo');
    if (existing) existing.style.display = 'none';
});

document.getElementById('file-banner').addEventListener('change', function () {
    if (!this.files[0]) return;
    var prev = document.getElementById('preview-banner');
    prev.src = URL.createObjectURL(this.files[0]);
    prev.style.display = 'inline-block';
    var existing = document.getElementById('existing-banner');
    if (existing) existing.style.display = 'none';
});
</script>
@stop
