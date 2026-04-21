@extends('adminlte::page')

@section('title', 'ตรวจสอบใบเสร็จ')

@section('content_header')
    <h1>ตรวจสอบใบเสร็จ #{{ $receipt->id }}</h1>
@stop

@section('content')
@if(session('success'))
    <div class="alert alert-success alert-dismissible">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        {{ session('error') }}
    </div>
@endif

<div class="row">
    {{-- รูปภาพ --}}
    <div class="col-md-7">
        <div class="card">
            <div class="card-header"><h3 class="card-title">รูปภาพใบเสร็จ ({{ count($receipt->images ?? []) }} รูป)</h3></div>
            <div class="card-body">
                <div class="row">
                    @foreach($receipt->images ?? [] as $img)
                    <div class="col-md-4 mb-3">
                        <a href="{{ Storage::url($img) }}" target="_blank">
                            <img src="{{ Storage::url($img) }}" class="img-fluid img-thumbnail"
                                 style="height:200px;object-fit:cover;width:100%;">
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- ข้อมูล + ดำเนินการ --}}
    <div class="col-md-5">
        <div class="card">
            <div class="card-header"><h3 class="card-title">ข้อมูลพนักงาน</h3></div>
            <div class="card-body">
                @php
                    $u = $receipt->user;
                    $b = ($u && $u->currentBranch) ? $u->currentBranch->branch : null;
                @endphp
                <dl class="row mb-0">
                    <dt class="col-sm-5">ชื่อ-นามสกุล</dt>
                    <dd class="col-sm-7">{{ $u ? $u->name . ' ' . $u->lastname : '-' }}</dd>
                    <dt class="col-sm-5">รหัสพนักงาน</dt>
                    <dd class="col-sm-7">{{ $u ? $u->employee_code : '-' }}</dd>
                    <dt class="col-sm-5">ระดับ</dt>
                    <dd class="col-sm-7">{{ $u ? ucfirst($u->level) : '-' }} (×{{ $u ? $u->level_multiplier : 1 }})</dd>
                    <dt class="col-sm-5">เขต/สาขา</dt>
                    <dd class="col-sm-7">{{ $b ? $b->zone . ' / ' . $b->shop_name : '-' }}</dd>
                    <dt class="col-sm-5">ส่งเมื่อ</dt>
                    <dd class="col-sm-7">{{ $receipt->created_at->format('d/m/Y H:i') }}</dd>
                    <dt class="col-sm-5">สถานะ</dt>
                    <dd class="col-sm-7">
                        @php $badge = ['pending'=>'warning','approved'=>'success','rejected'=>'danger'] @endphp
                        <span class="badge badge-{{ $badge[$receipt->status] }}">{{ $receipt->status }}</span>
                        @if($receipt->status === 'approved')
                            <span class="ml-2 text-success">+{{ number_format($receipt->points_awarded) }} คะแนน</span>
                        @endif
                    </dd>
                </dl>
            </div>
        </div>

        @if($receipt->status === 'pending')
        {{-- อนุมัติ --}}
        <div class="card card-success card-outline">
            <div class="card-header"><h3 class="card-title">อนุมัติ</h3></div>
            <div class="card-body">
                <form action="{{ route('admin.receipts.approve', $receipt) }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label>ยอดขาย (บาท) <span class="text-danger">*</span></label>
                        <input type="number" name="sales_amount" class="form-control" step="0.01" min="1" required>
                    </div>

                    <div id="sku-list">
                        <label>SKU (ถ้ามี)</label>
                        <div class="sku-row input-group mb-1">
                            <input type="text" name="sku_codes[]" class="form-control form-control-sm" placeholder="SKU code">
                            <input type="number" name="sku_qtys[]" class="form-control form-control-sm" placeholder="จำนวน" min="1" value="1">
                            <input type="number" name="sku_points[]" class="form-control form-control-sm" placeholder="คะแนน/ชิ้น" min="0" step="0.01">
                        </div>
                    </div>
                    <button type="button" class="btn btn-xs btn-secondary mb-2" onclick="addSku()">+ เพิ่ม SKU</button>

                    <div class="mt-2">
                        <button type="submit" class="btn btn-success btn-block">อนุมัติ</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ปฏิเสธ --}}
        <div class="card card-danger card-outline">
            <div class="card-header"><h3 class="card-title">ปฏิเสธ</h3></div>
            <div class="card-body">
                <form action="{{ route('admin.receipts.reject', $receipt) }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label>เหตุผล <span class="text-danger">*</span></label>
                        <textarea name="note" class="form-control" rows="3" required placeholder="ระบุเหตุผล..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-danger btn-block"
                            onclick="return confirm('ยืนยันปฏิเสธใบเสร็จนี้?')">ปฏิเสธ</button>
                </form>
            </div>
        </div>
        @elseif($receipt->note)
        <div class="card"><div class="card-body"><b>หมายเหตุ:</b> {{ $receipt->note }}</div></div>
        @endif
    </div>
</div>

<a href="{{ route('admin.receipts.index') }}" class="btn btn-secondary">
    <i class="fas fa-arrow-left"></i> กลับ
</a>
@stop

@push('js')
<script>
function addSku() {
    const div = document.createElement('div');
    div.className = 'sku-row input-group mb-1';
    div.innerHTML = `<input type="text" name="sku_codes[]" class="form-control form-control-sm" placeholder="SKU code">
        <input type="number" name="sku_qtys[]" class="form-control form-control-sm" placeholder="จำนวน" min="1" value="1">
        <input type="number" name="sku_points[]" class="form-control form-control-sm" placeholder="คะแนน/ชิ้น" min="0" step="0.01">
        <div class="input-group-append"><button type="button" class="btn btn-sm btn-danger" onclick="this.closest('.sku-row').remove()">×</button></div>`;
    document.getElementById('sku-list').appendChild(div);
}
</script>
@endpush
