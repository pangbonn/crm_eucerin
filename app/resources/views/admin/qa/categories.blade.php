@extends('adminlte::page')
@section('title','หมวดหมู่ Q&A')
@section('content_header')<h1>จัดการ Q&A</h1>@stop
@section('content')
@if(session('success'))<div class="alert alert-success alert-dismissible"><button class="close" data-dismiss="alert">&times;</button>{{ session('success') }}</div>@endif

<div class="row">
    <div class="col-md-5">
        <div class="card">
            <div class="card-header"><h3 class="card-title">หมวดหมู่ทั้งหมด</h3></div>
            <div class="card-body p-0">
                <table class="table table-sm">
                    <thead class="thead-light"><tr><th>ลำดับ</th><th>ชื่อหมวด</th><th>Q&A</th><th></th></tr></thead>
                    <tbody>
                        @forelse($categories as $cat)
                        <tr>
                            <td>{{ $cat->order }}</td>
                            <td>{{ $cat->name }}</td>
                            <td>{{ $cat->items_count }}</td>
                            <td class="text-nowrap">
                                <a href="{{ route('admin.qa-items.index', ['category'=>$cat->id]) }}" class="btn btn-xs btn-info">จัดการ Q&A</a>
                                <button class="btn btn-xs btn-warning" data-toggle="modal" data-target="#editCat{{ $cat->id }}">แก้ไข</button>
                                <form action="{{ route('admin.qa-categories.destroy', $cat) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('ลบหมวดนี้?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-xs btn-danger">ลบ</button>
                                </form>
                            </td>
                        </tr>

                        {{-- Edit Modal --}}
                        <div class="modal fade" id="editCat{{ $cat->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <form action="{{ route('admin.qa-categories.update', $cat) }}" method="POST">
                                    @csrf @method('PUT')
                                    <div class="modal-content">
                                        <div class="modal-header"><h5 class="modal-title">แก้ไขหมวดหมู่</h5>
                                            <button type="button" class="close" data-dismiss="modal">&times;</button></div>
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label>ชื่อหมวด</label>
                                                <input type="text" name="name" class="form-control" value="{{ $cat->name }}" required>
                                            </div>
                                            <div class="form-group">
                                                <label>ลำดับ</label>
                                                <input type="number" name="order" class="form-control" value="{{ $cat->order }}" min="0">
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
                        <tr><td colspan="4" class="text-center text-muted py-3">ยังไม่มีหมวดหมู่</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card card-primary card-outline">
            <div class="card-header"><h3 class="card-title">เพิ่มหมวดหมู่ใหม่</h3></div>
            <div class="card-body">
                <form action="{{ route('admin.qa-categories.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label>ชื่อหมวด <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>ลำดับ</label>
                        <input type="number" name="order" class="form-control" value="0" min="0">
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">เพิ่มหมวดหมู่</button>
                </form>
            </div>
        </div>
    </div>
</div>
@stop
