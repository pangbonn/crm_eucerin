@extends('adminlte::page')
@section('title','Q&A')
@section('content_header')<h1>จัดการ Q&A</h1>@stop
@section('content')
@if(session('success'))<div class="alert alert-success alert-dismissible"><button class="close" data-dismiss="alert">&times;</button>{{ session('success') }}</div>@endif

<div class="mb-2">
    <a href="{{ route('admin.qa-categories.index') }}" class="btn btn-sm btn-secondary">
        <i class="fas fa-tags"></i> จัดการหมวดหมู่
    </a>
</div>

@foreach($categories as $cat)
<div class="card">
    <div class="card-header bg-light">
        <h3 class="card-title">{{ $cat->name }}</h3>
    </div>
    <div class="card-body p-0">
        <table class="table table-sm">
            <thead class="thead-light"><tr><th>#</th><th>คำถาม</th><th>คำตอบ</th><th></th></tr></thead>
            <tbody>
                @forelse($cat->items as $item)
                <tr>
                    <td>{{ $item->order }}</td>
                    <td>{{ Str::limit($item->question, 60) }}</td>
                    <td><small class="text-muted">{{ Str::limit($item->answer, 80) }}</small></td>
                    <td class="text-nowrap">
                        <button class="btn btn-xs btn-warning" data-toggle="modal" data-target="#editItem{{ $item->id }}">แก้ไข</button>
                        <form action="{{ route('admin.qa-items.destroy', $item) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('ลบ Q&A นี้?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-xs btn-danger">ลบ</button>
                        </form>
                    </td>
                </tr>

                {{-- Edit Modal --}}
                <div class="modal fade" id="editItem{{ $item->id }}" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <form action="{{ route('admin.qa-items.update', $item) }}" method="POST">
                            @csrf @method('PUT')
                            <div class="modal-content">
                                <div class="modal-header"><h5 class="modal-title">แก้ไข Q&A</h5>
                                    <button type="button" class="close" data-dismiss="modal">&times;</button></div>
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label>หมวดหมู่</label>
                                        <select name="category_id" class="form-control">
                                            @foreach($categories as $c)
                                                <option value="{{ $c->id }}" {{ $item->category_id==$c->id?'selected':'' }}>{{ $c->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>คำถาม <span class="text-danger">*</span></label>
                                        <textarea name="question" class="form-control" rows="2" required>{{ $item->question }}</textarea>
                                    </div>
                                    <div class="form-group">
                                        <label>คำตอบ <span class="text-danger">*</span></label>
                                        <textarea name="answer" class="form-control" rows="4" required>{{ $item->answer }}</textarea>
                                    </div>
                                    <div class="form-group">
                                        <label>ลำดับ</label>
                                        <input type="number" name="order" class="form-control" value="{{ $item->order }}" min="0">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">ยกเลิก</button>
                                    <button type="submit" class="btn btn-primary">บันทึก</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                @empty
                <tr><td colspan="4" class="text-center text-muted py-2">ยังไม่มี Q&A ในหมวดนี้</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endforeach

{{-- เพิ่ม Q&A ใหม่ --}}
<div class="card card-primary card-outline" style="max-width:600px">
    <div class="card-header"><h3 class="card-title">เพิ่ม Q&A ใหม่</h3></div>
    <div class="card-body">
        <form action="{{ route('admin.qa-items.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label>หมวดหมู่ <span class="text-danger">*</span></label>
                <select name="category_id" class="form-control" required>
                    <option value="">-- เลือกหมวดหมู่ --</option>
                    @foreach($categories as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>คำถาม <span class="text-danger">*</span></label>
                <textarea name="question" class="form-control" rows="2" required></textarea>
            </div>
            <div class="form-group">
                <label>คำตอบ <span class="text-danger">*</span></label>
                <textarea name="answer" class="form-control" rows="4" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary btn-block">เพิ่ม Q&A</button>
        </form>
    </div>
</div>
@stop
