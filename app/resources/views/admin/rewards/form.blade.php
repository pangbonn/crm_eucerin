@extends('adminlte::page')
@section('title', $reward->exists ? 'แก้ไขรางวัล' : 'เพิ่มรางวัล')
@section('content_header')<h1>{{ $reward->exists ? 'แก้ไข: '.$reward->name : 'เพิ่มของรางวัลใหม่' }}</h1>@stop
@section('content')
<div class="card" style="max-width:600px">
    <div class="card-body">
        <form action="{{ $reward->exists ? route('admin.rewards.update',$reward) : route('admin.rewards.store') }}"
              method="POST" enctype="multipart/form-data">
            @csrf
            @if($reward->exists) @method('PUT') @endif

            <div class="form-group">
                <label>ชื่อของรางวัล <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name', $reward->name) }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label>รายละเอียด</label>
                <textarea name="description" class="form-control" rows="3">{{ old('description', $reward->description) }}</textarea>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>คะแนนที่ต้องใช้ <span class="text-danger">*</span></label>
                        <input type="number" name="points_required" class="form-control" min="0"
                               value="{{ old('points_required', $reward->points_required ?? 0) }}" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Training คะแนนขั้นต่ำ</label>
                        <input type="number" name="training_points_required" class="form-control" min="0"
                               value="{{ old('training_points_required', $reward->training_points_required ?? 0) }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Stock <span class="text-danger">*</span></label>
                        <input type="number" name="stock" class="form-control" min="0"
                               value="{{ old('stock', $reward->stock ?? 0) }}" required>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>รูปภาพ</label>
                @if($reward->exists && $reward->image)
                    <div class="mb-2">
                        <img src="{{ Storage::url($reward->image) }}" style="height:80px;object-fit:cover;" class="img-thumbnail">
                    </div>
                @endif
                <input type="file" name="image" class="form-control-file" accept=".jpg,.jpeg,.png">
                <small class="text-muted">JPG/PNG ขนาดไม่เกิน 2MB</small>
            </div>

            <div class="form-group">
                <div class="custom-control custom-switch">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1"
                           {{ old('is_active', $reward->is_active ?? true) ? 'checked' : '' }}>
                    <label class="custom-control-label" for="is_active">เปิดให้แลก</label>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">บันทึก</button>
            <a href="{{ route('admin.rewards.index') }}" class="btn btn-secondary ml-2">ยกเลิก</a>
        </form>
    </div>
</div>
@stop
