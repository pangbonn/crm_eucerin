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
                        <a href="{{ route('admin.file.serve', $img) }}" target="_blank">
                            <img src="{{ route('admin.file.serve', $img) }}" class="img-fluid img-thumbnail"
                                 style="height:200px;object-fit:cover;width:100%;">
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- SKU / สินค้า --}}
        @if(!empty($receipt->sku_data))
        <div class="card">
            <div class="card-header"><h3 class="card-title">รายการสินค้า (SKU)</h3></div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>SKU Code</th>
                            <th class="text-center">จำนวน</th>
                            <th class="text-right">คะแนน/ชิ้น</th>
                            <th class="text-right">รวม</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $skuTotal = 0; @endphp
                        @foreach($receipt->sku_data as $sku)
                        @php
                            $qty      = (int)($sku['qty'] ?? 1);
                            $pts      = (float)($sku['points'] ?? 0);
                            $subtotal = $qty * $pts;
                            $skuTotal += $subtotal;
                        @endphp
                        <tr>
                            <td><code>{{ $sku['sku'] ?? '-' }}</code></td>
                            <td class="text-center">{{ $qty }}</td>
                            <td class="text-right">{{ number_format($pts, 2) }}</td>
                            <td class="text-right text-success">{{ number_format($subtotal, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="font-weight-bold">
                        <tr>
                            <td colspan="3" class="text-right">รวมคะแนน SKU</td>
                            <td class="text-right text-success">{{ number_format($skuTotal, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        @endif

        {{-- ประวัติคะแนนของใบเสร็จนี้ --}}
        @if($receipt->pointHistory->isNotEmpty())
        <div class="card">
            <div class="card-header"><h3 class="card-title">ประวัติคะแนน (ใบเสร็จนี้)</h3></div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <thead class="thead-light">
                        <tr><th>วันที่</th><th>คะแนน</th><th>หมายเหตุ</th></tr>
                    </thead>
                    <tbody>
                        @foreach($receipt->pointHistory as $p)
                        <tr>
                            <td><small>{{ $p->created_at->format('d/m/Y H:i') }}</small></td>
                            <td class="{{ $p->points >= 0 ? 'text-success' : 'text-danger' }} font-weight-bold">
                                {{ $p->points >= 0 ? '+' : '' }}{{ number_format($p->points) }}
                            </td>
                            <td><small>{{ $p->note }}</small></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
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
                    <dd class="col-sm-7">{{ $b ? ($b->zone->name ?? '-') . ' / ' . $b->shop_name : '-' }}</dd>
                    <dt class="col-sm-5">ส่งเมื่อ</dt>
                    <dd class="col-sm-7">{{ $receipt->created_at->format('d/m/Y H:i') }}</dd>
                    <dt class="col-sm-5">สถานะ</dt>
                    <dd class="col-sm-7">
                        @php $badge = ['pending'=>'warning','approved'=>'success','rejected'=>'danger','cancelled'=>'secondary'] @endphp
                        <span class="badge badge-{{ $badge[$receipt->status] }}">{{ $receipt->status }}</span>
                        @if($receipt->status === 'approved')
                            <span class="ml-2 text-success font-weight-bold">+{{ number_format($receipt->points_awarded) }} คะแนน</span>
                        @endif
                    </dd>
                    @if($receipt->status !== 'pending' && $receipt->approver)
                    <dt class="col-sm-5">ดำเนินการโดย</dt>
                    <dd class="col-sm-7"><small>{{ $receipt->approver->name }}<br>{{ $receipt->approved_at ? $receipt->approved_at->format('d/m/Y H:i') : '' }}</small></dd>
                    @endif
                    @if($receipt->sales_amount)
                    <dt class="col-sm-5">ยอดขาย</dt>
                    <dd class="col-sm-7">{{ number_format($receipt->sales_amount, 2) }} บาท</dd>
                    @endif
                </dl>
            </div>
        </div>

        @if($receipt->status === 'pending' || $receipt->status === 'approved')
        {{-- อนุมัติ / แก้ไขคะแนน --}}
        <div class="card card-success card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    @if($receipt->status === 'approved')
                        <i class="fas fa-edit"></i> แก้ไขคะแนน
                    @else
                        <i class="fas fa-check"></i> อนุมัติ
                    @endif
                </h3>
            </div>
            <div class="card-body">
                @if($receipt->status === 'approved')
                    <div class="alert alert-warning alert-sm py-1 px-2 mb-2">
                        <small><i class="fas fa-info-circle"></i> คะแนนเดิม {{ number_format($receipt->points_awarded) }} จะถูกยกเลิกและออกคะแนนใหม่</small>
                    </div>
                @endif
                <form action="{{ route('admin.receipts.approve', $receipt) }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label>ยอดขาย (บาท) <span class="text-danger">*</span></label>
                        <input type="number" name="sales_amount" class="form-control" step="0.01" min="1" required
                               value="{{ $receipt->sales_amount ?? '' }}">
                    </div>

                    <div id="sku-list">
                        <label>SKU (ถ้ามี)</label>
                        @if(!empty($receipt->sku_data))
                            @foreach($receipt->sku_data as $sku)
                            <div class="sku-row input-group mb-1">
                                <input type="text" name="sku_codes[]" class="form-control form-control-sm" placeholder="SKU code" value="{{ $sku['sku'] ?? '' }}">
                                <input type="number" name="sku_qtys[]" class="form-control form-control-sm" placeholder="จำนวน" min="1" value="{{ $sku['qty'] ?? 1 }}">
                                <input type="number" name="sku_points[]" class="form-control form-control-sm" placeholder="คะแนน/ชิ้น" min="0" step="0.01" value="{{ $sku['points'] ?? 0 }}">
                                <div class="input-group-append"><button type="button" class="btn btn-sm btn-danger" onclick="this.closest('.sku-row').remove()">×</button></div>
                            </div>
                            @endforeach
                        @else
                        <div class="sku-row input-group mb-1">
                            <input type="text" name="sku_codes[]" class="form-control form-control-sm" placeholder="SKU code">
                            <input type="number" name="sku_qtys[]" class="form-control form-control-sm" placeholder="จำนวน" min="1" value="1">
                            <input type="number" name="sku_points[]" class="form-control form-control-sm" placeholder="คะแนน/ชิ้น" min="0" step="0.01">
                        </div>
                        @endif
                    </div>
                    <button type="button" class="btn btn-xs btn-secondary mb-2" onclick="addSku()">+ เพิ่ม SKU</button>

                    <div class="mt-2">
                        <button type="submit" class="btn btn-success btn-block"
                                @if($receipt->status === 'approved') onclick="return confirm('ยืนยันแก้ไขคะแนน? คะแนนเดิมจะถูกยกเลิก')" @endif>
                            @if($receipt->status === 'approved') แก้ไขคะแนน @else อนุมัติ @endif
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @endif

        @if($receipt->status === 'pending')
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
        @elseif($receipt->status === 'approved')
        {{-- ยกเลิกการอนุมัติ --}}
        <div class="card card-secondary card-outline">
            <div class="card-header"><h3 class="card-title"><i class="fas fa-ban"></i> ยกเลิกการอนุมัติ</h3></div>
            <div class="card-body">
                <div class="alert alert-danger alert-sm py-1 px-2 mb-2">
                    <small><i class="fas fa-exclamation-triangle"></i> คะแนน {{ number_format($receipt->points_awarded) }} จะถูกหักออกจากพนักงาน</small>
                </div>
                <form action="{{ route('admin.receipts.cancel', $receipt) }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label>เหตุผล <span class="text-danger">*</span></label>
                        <textarea name="note" class="form-control" rows="3" required placeholder="ระบุเหตุผล..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-secondary btn-block"
                            onclick="return confirm('ยืนยันยกเลิกการอนุมัติ? คะแนนจะถูกหักออก')">ยกเลิกการอนุมัติ</button>
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
