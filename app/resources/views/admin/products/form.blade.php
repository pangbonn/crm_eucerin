@extends('adminlte::page')
@section('title', $product->exists ? 'แก้ไขสินค้า' : 'เพิ่มสินค้า')
@section('content_header')<h1>{{ $product->exists ? 'แก้ไข: '.$product->name : 'เพิ่มสินค้าใหม่' }}</h1>@stop

@section('content')
<div class="card" style="max-width:700px">
    <div class="card-body">
        <form action="{{ $product->exists ? route('admin.products.update', $product) : route('admin.products.store') }}"
              method="POST" enctype="multipart/form-data">
            @csrf
            @if($product->exists) @method('PUT') @endif

            <div class="form-group">
                <label>ชื่อสินค้า <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name', $product->name) }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label>รายละเอียด</label>
                <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="4">{{ old('description', $product->description) }}</textarea>
                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label>รูปสินค้า</label>
                <div class="mb-2">
                    @if($product->exists && $product->image)
                        <img id="existing-image" src="{{ Storage::url($product->image) }}" style="height:120px;object-fit:cover;" class="img-thumbnail">
                    @endif
                    <img id="preview-image" src="#" alt="Preview" class="img-thumbnail" style="height:120px;object-fit:cover;display:none;">
                </div>
                <input type="file" id="file-image" name="image" class="form-control-file @error('image') is-invalid @enderror" accept=".jpg,.jpeg,.png">
                <small class="text-muted">JPG/PNG ขนาดไม่เกิน 2MB</small>
                @error('image')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <div class="custom-control custom-switch">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1"
                           {{ old('is_active', $product->is_active ?? true) ? 'checked' : '' }}>
                    <label class="custom-control-label" for="is_active">สถานะเปิดใช้งาน</label>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">บันทึก</button>
            <a href="{{ route('admin.products.index') }}" class="btn btn-secondary ml-2">ยกเลิก</a>
        </form>
    </div>
</div>
@stop

@section('js')
<script>
document.getElementById('file-image').addEventListener('change', function () {
    if (!this.files[0]) return;
    var preview = document.getElementById('preview-image');
    preview.src = URL.createObjectURL(this.files[0]);
    preview.style.display = 'inline-block';
    var existing = document.getElementById('existing-image');
    if (existing) existing.style.display = 'none';
});
</script>
@stop
