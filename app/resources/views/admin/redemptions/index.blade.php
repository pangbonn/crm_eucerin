@extends('adminlte::page')
@section('title','การแลกของรางวัล')
@section('content_header')<h1>การแลกของรางวัล</h1>@stop
@section('content')
@if(session('success'))<div class="alert alert-success alert-dismissible"><button class="close" data-dismiss="alert">&times;</button>{{ session('success') }}</div>@endif
@if(session('error'))<div class="alert alert-danger alert-dismissible"><button class="close" data-dismiss="alert">&times;</button>{{ session('error') }}</div>@endif

<div class="card">
    <div class="card-header p-0">
        <ul class="nav nav-tabs">
            @foreach(['pending'=>'รอตรวจสอบ','approved'=>'อนุมัติแล้ว','rejected'=>'ปฏิเสธแล้ว'] as $key=>$label)
            <li class="nav-item">
                <a class="nav-link {{ $tab===$key?'active':'' }}"
                   href="{{ route('admin.redemptions.index', ['tab'=>$key]) }}">
                    {{ $label }}
                    @if($key==='pending')<span class="badge badge-warning">{{ $pendingCount }}</span>@endif
                </a>
            </li>
            @endforeach
        </ul>
        <div class="p-2">
            <a href="{{ route('admin.redemptions.export') }}" class="btn btn-sm btn-success">
                <i class="fas fa-file-excel"></i> Export จัดส่ง
            </a>
        </div>
    </div>
    <div class="card-body p-0">
        <table class="table table-sm table-hover">
            <thead class="thead-light">
                <tr><th>พนักงาน</th><th>ของรางวัล</th><th>ที่อยู่จัดส่ง</th><th>วันที่</th><th></th></tr>
            </thead>
            <tbody>
                @forelse($redemptions as $r)
                <tr>
                    <td>
                        <b>{{ $r->user ? $r->user->name.' '.$r->user->lastname : '-' }}</b>
                        <br><small class="text-muted">{{ $r->user ? $r->user->employee_code : '' }}</small>
                    </td>
                    <td>
                        {{ $r->reward ? $r->reward->name : '-' }}
                        <br><small class="text-muted">{{ $r->reward ? number_format($r->reward->points_required).' คะแนน' : '' }}</small>
                    </td>
                    <td>
                        <small>{{ $r->shipping_name }} | {{ $r->shipping_phone }}</small>
                        <br><small class="text-muted">{{ $r->shipping_province }} {{ $r->shipping_postal_code }}</small>
                    </td>
                    <td><small>{{ $r->created_at->format('d/m/Y') }}</small></td>
                    <td>
                        @if($tab === 'pending')
                        <form action="{{ route('admin.redemptions.approve', $r) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('อนุมัติการแลกรางวัลนี้?')">
                            @csrf
                            <button class="btn btn-xs btn-success">อนุมัติ</button>
                        </form>
                        <button class="btn btn-xs btn-danger" data-toggle="modal"
                                data-target="#rejectModal{{ $r->id }}">ปฏิเสธ</button>

                        {{-- Reject Modal --}}
                        <div class="modal fade" id="rejectModal{{ $r->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <form action="{{ route('admin.redemptions.reject', $r) }}" method="POST">
                                    @csrf
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">ปฏิเสธการแลกรางวัล</h5>
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label>เหตุผล <span class="text-danger">*</span></label>
                                                <textarea name="note" class="form-control" rows="3" required></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">ยกเลิก</button>
                                            <button type="submit" class="btn btn-danger">ปฏิเสธ + คืนคะแนน</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        @else
                            @php $badge = ['approved'=>'success','rejected'=>'danger'] @endphp
                            <span class="badge badge-{{ $badge[$r->status] ?? 'secondary' }}">{{ $r->status }}</span>
                            @if($r->note)<br><small class="text-muted">{{ $r->note }}</small>@endif
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-muted py-4">ไม่มีรายการ</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">{{ $redemptions->links() }}</div>
</div>
@stop
