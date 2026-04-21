@extends('adminlte::page')
@section('title','คะแนน - '.$user->name)
@section('content_header')<h1>คะแนน: {{ $user->name }} {{ $user->lastname }}</h1>@stop
@section('content')
@if(session('success'))<div class="alert alert-success alert-dismissible"><button class="close" data-dismiss="alert">&times;</button>{{ session('success') }}</div>@endif

<div class="row">
    <div class="col-md-4">
        <div class="info-box bg-primary">
            <span class="info-box-icon"><i class="fas fa-star"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">คะแนนรวม</span>
                <span class="info-box-number">{{ number_format($totalPoints) }}</span>
            </div>
        </div>

        <div class="card card-warning card-outline">
            <div class="card-header"><h3 class="card-title">ปรับคะแนน Manual</h3></div>
            <div class="card-body">
                <form action="{{ route('admin.points.adjust', $user) }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label>คะแนน (ติดลบ = ลดคะแนน)</label>
                        <input type="number" name="points" class="form-control" required placeholder="+100 หรือ -50">
                    </div>
                    <div class="form-group">
                        <label>เหตุผล <span class="text-danger">*</span></label>
                        <input type="text" name="note" class="form-control" required>
                    </div>
                    <button class="btn btn-warning btn-block">ปรับคะแนน</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-header"><h3 class="card-title">ประวัติคะแนน</h3></div>
            <div class="card-body p-0">
                <table class="table table-sm">
                    <thead class="thead-light">
                        <tr><th>วันที่</th><th>แหล่งที่มา</th><th>คะแนน</th><th>หมายเหตุ</th></tr>
                    </thead>
                    <tbody>
                        @foreach($points as $p)
                        <tr>
                            <td><small>{{ $p->created_at->format('d/m/Y H:i') }}</small></td>
                            <td><span class="badge badge-info">{{ $p->source }}</span></td>
                            <td class="{{ $p->points >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ $p->points >= 0 ? '+' : '' }}{{ number_format($p->points) }}
                            </td>
                            <td><small>{{ $p->note }}</small></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer">{{ $points->links() }}</div>
        </div>
    </div>
</div>
<a href="{{ route('admin.points.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> กลับ</a>
@stop
