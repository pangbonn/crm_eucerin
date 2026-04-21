@extends('adminlte::page')
@section('title', $banner->exists ? 'แก้ไข Banner' : 'เพิ่ม Banner')
@section('content_header')<h1>{{ $banner->exists ? 'แก้ไข Banner' : 'เพิ่ม Banner ใหม่' }}</h1>@stop
@section('content')
<div class="card" style="max-width:550px">
    <div class="card-body">
        <form action="{{ $banner->exists ? route('admin.banners.update',$banner) : route('admin.banners.store') }}"
              method="POST" enctype="multipart/form-data">
            @csrf
            @if($banner->exists) @method('PUT') @endif

            <div class="form-group">
                <label>ประเภท <span class="text-danger">*</span></label>
                <select name="type" class="form-control" required>
                    @foreach(['main'=>'หน้าหลัก','receipt'=>'ส่งใบเสร็จ','exam'=>'แบบทดสอบ','reward'=>'แลกรางวัล'] as $v=>$l)
                        <option value="{{ $v }}" {{ old('type', $banner->type)===$v?'selected':'' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>รูปภาพ Banner</label>
                @if($banner->exists && $banner->image_url)
                    <div class="mb-2">
                        <img src="{{ filter_var($banner->image_url, FILTER_VALIDATE_URL) ? $banner->image_url : Storage::url($banner->image_url) }}"
                             style="height:80px;object-fit:cover;" class="img-thumbnail">
                    </div>
                @endif
                <input type="file" name="image_url" class="form-control-file" accept=".jpg,.jpeg,.png">
            </div>

            <div class="form-group">
                <label>ข้อความเงื่อนไข</label>
                <textarea name="condition_text" class="form-control" rows="2">{{ old('condition_text', $banner->condition_text) }}</textarea>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>เดือนที่แสดง</label>
                        <select name="display_month" class="form-control">
                            <option value="">ไม่กำหนด</option>
                            @foreach(range(1,12) as $m)
                                <option value="{{ $m }}" {{ old('display_month', $banner->display_month)==$m?'selected':'' }}>{{ $m }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>ปี</label>
                        <select name="display_year" class="form-control">
                            <option value="">ไม่กำหนด</option>
                            @foreach(range(now()->year, now()->year+2) as $y)
                                <option value="{{ $y }}" {{ old('display_year', $banner->display_year)==$y?'selected':'' }}>{{ $y }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group mt-4">
                        <div class="custom-control custom-switch">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1"
                                   {{ old('is_active', $banner->is_active ?? true) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="is_active">Active</label>
                        </div>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">บันทึก</button>
            <a href="{{ route('admin.banners.index') }}" class="btn btn-secondary ml-2">ยกเลิก</a>
        </form>
    </div>
</div>
@stop
