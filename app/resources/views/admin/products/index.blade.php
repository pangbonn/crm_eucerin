@extends('adminlte::page')
@section('title', 'จัดการสินค้า')
@section('content_header')<h1>จัดการสินค้า</h1>@stop

@section('content')
@if(session('success'))
<div class="alert alert-success alert-dismissible">
    <button class="close" data-dismiss="alert">&times;</button>
    {{ session('success') }}
</div>
@endif

<div class="card">
    <div class="card-header">
        <div class="card-tools">
            <a href="{{ route('admin.products.create') }}" class="btn btn-sm btn-primary">
                <i class="fas fa-plus"></i> เพิ่มสินค้า
            </a>
        </div>
    </div>

    <div class="card-body p-0">
        <table class="table table-hover">
            <thead class="thead-light">
                <tr>
                    <th>รูป</th>
                    <th>ชื่อสินค้า</th>
                    <th>รายละเอียด</th>
                    <th>สถานะ</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                <tr class="{{ !$product->is_active ? 'table-secondary text-muted' : '' }}">
                    <td>
                        @if($product->image)
                            <img src="{{ Storage::url($product->image) }}" style="height:40px;width:40px;object-fit:cover;" class="img-thumbnail">
                        @else
                            <i class="fas fa-box-open fa-2x text-muted"></i>
                        @endif
                    </td>
                    <td>{{ $product->name }}</td>
                    <td>{{ \Illuminate\Support\Str::limit($product->description, 100) ?: '-' }}</td>
                    <td>
                        @if($product->is_active)
                            <span class="badge badge-success">เปิด</span>
                        @else
                            <span class="badge badge-secondary">ปิด</span>
                        @endif
                    </td>
                    <td class="text-nowrap">
                        <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-xs btn-warning">แก้ไข</a>
                        <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('ลบสินค้านี้?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-xs btn-danger">ลบ</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">ยังไม่มีสินค้า</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="card-footer">{{ $products->links() }}</div>
</div>
@stop
