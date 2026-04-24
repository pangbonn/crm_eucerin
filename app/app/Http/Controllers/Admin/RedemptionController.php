<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RewardRedemption;
use App\Services\PointCalculationService;
use App\Exports\RedemptionsExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class RedemptionController extends Controller
{
    private PointCalculationService $pointService;

    public function __construct(PointCalculationService $pointService)
    {
        $this->pointService = $pointService;
    }

    public function index(Request $request)
    {
        $tab   = $request->get('tab', 'pending');
        $query = RewardRedemption::with(['user', 'reward'])->orderByDesc('created_at');

        if (in_array($tab, ['pending', 'approved', 'rejected'])) {
            $query->where('status', $tab);
        }

        $redemptions    = $query->paginate(20)->withQueryString();
        $pendingCount   = RewardRedemption::where('status', 'pending')->count();

        return view('admin.redemptions.index', compact('redemptions', 'tab', 'pendingCount'));
    }

    public function approve(RewardRedemption $redemption)
    {
        if ($redemption->status !== 'pending') {
            return back()->with('error', 'รายการนี้ดำเนินการแล้ว');
        }

        $reward = $redemption->reward;
        if ($reward && $reward->stock > 0) {
            $reward->decrement('stock');
        }

        $redemption->update([
            'status'      => 'approved',
            'approved_by' => Auth::guard('admin')->id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'อนุมัติการแลกรางวัลเรียบร้อย');
    }

    public function reject(Request $request, RewardRedemption $redemption)
    {
        if ($redemption->status !== 'pending') {
            return back()->with('error', 'รายการนี้ดำเนินการแล้ว');
        }

        $request->validate(['note' => 'required|string|max:300']);

        // คืนคะแนนให้ user
        $this->pointService->refundRedemptionPoints(
            $redemption->user,
            $redemption->reward ? $redemption->reward->points_required : 0,
            $redemption->id
        );

        $redemption->update([
            'status'      => 'rejected',
            'approved_by' => Auth::guard('admin')->id(),
            'approved_at' => now(),
            'note'        => $request->note,
        ]);

        return back()->with('success', 'ปฏิเสธและคืนคะแนนเรียบร้อย');
    }

    public function export()
    {
        return Excel::download(new RedemptionsExport, 'redemptions_' . now()->format('Ymd') . '.xlsx');
    }

    public function updateTracking(Request $request, RewardRedemption $redemption)
    {
        if ($redemption->status !== 'approved') {
            return back()->with('error', 'แก้ไข Tracking ได้เฉพาะรายการที่อนุมัติแล้ว');
        }

        $data = $request->validate([
            'shipping_carrier' => 'required|string|max:100',
            'tracking_number' => 'required|string|max:100',
        ]);

        $redemption->update($data);

        return back()->with('success', 'บันทึกข้อมูลขนส่งและ Tracking เรียบร้อย');
    }
}
