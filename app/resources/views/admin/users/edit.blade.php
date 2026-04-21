@extends('adminlte::page')

@section('title', 'แก้ไขข้อมูลพนักงาน')

@section('content_header')
    <h1>แก้ไขข้อมูลพนักงาน: {{ $user->name }} {{ $user->lastname }}</h1>
@stop

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.users.update', $user) }}" method="POST">
            @csrf @method('PUT')

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>ชื่อ</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $user->name) }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>นามสกุล</label>
                        <input type="text" name="lastname" class="form-control @error('lastname') is-invalid @enderror"
                               value="{{ old('lastname', $user->lastname) }}" required>
                        @error('lastname')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>เบอร์โทร</label>
                        <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
                               value="{{ old('phone', $user->phone) }}" required>
                        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>รหัสพนักงาน</label>
                        <input type="text" name="employee_code" class="form-control @error('employee_code') is-invalid @enderror"
                               value="{{ old('employee_code', $user->employee_code) }}" required>
                        @error('employee_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>ระดับ</label>
                        <select name="level" class="form-control @error('level') is-invalid @enderror" required>
                            @foreach(['gold','silver','platinum'] as $lvl)
                                <option value="{{ $lvl }}" {{ old('level',$user->level)===$lvl?'selected':'' }}>{{ ucfirst($lvl) }}</option>
                            @endforeach
                        </select>
                        @error('level')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <div class="mt-2">
                <button type="submit" class="btn btn-primary">บันทึก</button>
                <a href="{{ route('admin.users.show', $user) }}" class="btn btn-secondary ml-2">ยกเลิก</a>
            </div>
        </form>
    </div>
</div>
@stop
