@extends('adminlte::page')

@section('title', 'อนุมัติใบเสร็จ')

@section('content_header')
    <h1>อนุมัติใบเสร็จ</h1>
@stop

@section('content')
@if(session('success'))
    <div class="alert alert-success alert-dismissible">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        {{ session('success') }}
    </div>
@endif

<div class="card">
    <div class="card-header p-0">
        <ul class="nav nav-tabs" id="receiptTabs">
            @foreach(['pending'=>'รอตรวจสอบ','approved'=>'อนุมัติแล้ว','rejected'=>'ปฏิเสธแล้ว','cancelled'=>'ยกเลิกแล้ว'] as $key=>$label)
            <li class="nav-item">
                <a class="nav-link {{ $tab===$key?'active':'' }}"
                   href="{{ route('admin.receipts.index', ['tab'=>$key]) }}">
                    {{ $label }}
                    @if($key==='pending')
                        <span class="badge badge-warning">{{ \App\Models\Receipt::where('status','pending')->count() }}</span>
                    @endif
                </a>
            </li>
            @endforeach
        </ul>
        <div class="p-2 text-right">
            <a href="{{ route('admin.receipts.export.products') }}" class="btn btn-sm btn-success">
                <i class="fas fa-file-excel"></i> Export รายการสินค้า (ตามใบเสร็จ)
            </a>
        </div>
    </div>

    <div class="card-body p-0">
        <table class="table table-sm table-hover">
            <thead class="thead-light">
                <tr><th>พนักงาน</th><th>เขต/สาขา</th><th>จำนวนรูป</th><th>วันที่ส่ง</th><th>คะแนน</th><th></th></tr>
            </thead>
            <tbody>
                @forelse($receipts as $receipt)
                <tr>
                    <td>{{ $receipt->user ? $receipt->user->name : '-' }} {{ $receipt->user ? $receipt->user->lastname : '' }}</td>
                    @php
                        $branch = ($receipt->user && $receipt->user->currentBranch) ? $receipt->user->currentBranch->branch : null;
                    @endphp
                    <td><small class="text-muted">{{ $branch ? ($branch->zone->name ?? '-') . ' / ' . $branch->shop_name : '-' }}</small></td>
                    <td>{{ count($receipt->images ?? []) }} รูป</td>
                    <td><small>{{ $receipt->created_at->format('d/m/Y H:i') }}</small></td>
                    <td>
                        @if($receipt->status === 'approved')
                            <span class="text-success">+{{ number_format($receipt->points_awarded) }}</span>
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.receipts.show', $receipt) }}" class="btn btn-xs btn-primary">ดู / ดำเนินการ</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted py-4">ไม่มีรายการ</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">{{ $receipts->links() }}</div>
</div>
@stop
