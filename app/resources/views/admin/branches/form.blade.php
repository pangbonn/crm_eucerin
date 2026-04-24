@extends('adminlte::page')

@section('title', $branch->exists ? 'แก้ไขสาขา' : 'เพิ่มสาขา')

@section('content_header')
    <h1>{{ $branch->exists ? 'แก้ไขสาขา' : 'เพิ่มสาขาใหม่' }}</h1>
@stop

@section('content')
<div class="card" style="max-width:600px">
    <div class="card-body">
        <form action="{{ $branch->exists ? route('admin.branches.update', $branch) : route('admin.branches.store') }}"
              method="POST">
            @csrf
            @if($branch->exists) @method('PUT') @endif

            <div class="form-group">
                <label>เขต <span class="text-danger">*</span></label>
                <select name="zone_id" class="form-control @error('zone_id') is-invalid @enderror" required>
                    <option value="">-- เลือกเขต --</option>
                    @foreach($zones as $z)
                        <option value="{{ $z->id }}" {{ old('zone_id', $branch->zone_id)==$z->id?'selected':'' }}>{{ $z->name }}</option>
                    @endforeach
                </select>
                @error('zone_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label>จังหวัด</label>
                <select name="province_id" class="form-control">
                    <option value="">-- เลือกจังหวัด --</option>
                    @foreach($provinces as $p)
                        <option value="{{ $p->id }}" {{ old('province_id', $branch->province_id)==$p->id?'selected':'' }}>
                            {{ $p->name_th }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>ประเภทร้าน (Channel) <span class="text-danger">*</span></label>
                <select name="shop_type_id" class="form-control @error('shop_type_id') is-invalid @enderror" required>
                    <option value="">-- เลือกประเภทร้าน --</option>
                    @foreach($shopTypes as $t)
                        <option value="{{ $t->id }}" {{ old('shop_type_id', $branch->shop_type_id)==$t->id?'selected':'' }}>{{ $t->name }}</option>
                    @endforeach
                </select>
                @error('shop_type_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label>ชื่อร้าน/สาขา <span class="text-danger">*</span></label>
                <input type="text" name="shop_name" class="form-control @error('shop_name') is-invalid @enderror"
                       value="{{ old('shop_name', $branch->shop_name) }}" required>
                @error('shop_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            @if($branch->exists)
            <div class="form-group">
                <div class="custom-control custom-switch">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1"
                           {{ old('is_active', $branch->is_active) ? 'checked' : '' }}>
                    <label class="custom-control-label" for="is_active">เปิดใช้งาน</label>
                </div>
            </div>
            @endif

            <div class="mt-3">
                <button type="submit" class="btn btn-primary">บันทึก</button>
                <a href="{{ route('admin.branches.index') }}" class="btn btn-secondary ml-2">ยกเลิก</a>
            </div>
        </form>
    </div>
</div>
@stop
