<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Receipt;
use App\Services\PointCalculationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReceiptController extends Controller
{
    private PointCalculationService $pointService;

    public function __construct(PointCalculationService $pointService)
    {
        $this->pointService = $pointService;
    }

    public function index(Request $request)
    {
        $tab   = $request->get('tab', 'pending');
        $query = Receipt::with('user')->orderByDesc('created_at');

        if ($tab === 'pending') {
            $query->where('status', 'pending');
        } elseif ($tab === 'approved') {
            $query->where('status', 'approved');
        } elseif ($tab === 'rejected') {
            $query->where('status', 'rejected');
        }

        $receipts = $query->paginate(20)->withQueryString();

        return view('admin.receipts.index', compact('receipts', 'tab'));
    }

    public function show(Receipt $receipt)
    {
        $receipt->load('user.currentBranch.branch');
        return view('admin.receipts.show', compact('receipt'));
    }

    public function approve(Request $request, Receipt $receipt)
    {
        if ($receipt->status !== 'pending') {
            return back()->with('error', 'ใบเสร็จนี้ถูกดำเนินการแล้ว');
        }

        $request->validate([
            'sales_amount' => 'required|numeric|min:1',
        ]);

        $skuData = [];
        if ($request->filled('sku_codes')) {
            foreach ($request->sku_codes as $i => $code) {
                if (!$code) continue;
                $skuData[] = [
                    'sku'    => $code,
                    'qty'    => (int)($request->sku_qtys[$i] ?? 1),
                    'points' => (float)($request->sku_points[$i] ?? 0),
                ];
            }
        }

        $points = $this->pointService->approveReceipt(
            $receipt,
            (float)$request->sales_amount,
            $skuData,
            Auth::guard('admin')->user()
        );

        return back()->with('success', "อนุมัติเรียบร้อย ให้คะแนน {$points} คะแนน");
    }

    public function reject(Request $request, Receipt $receipt)
    {
        if ($receipt->status !== 'pending') {
            return back()->with('error', 'ใบเสร็จนี้ถูกดำเนินการแล้ว');
        }

        $request->validate(['note' => 'required|string|max:500']);

        $this->pointService->rejectReceipt(
            $receipt,
            $request->note,
            Auth::guard('admin')->user()
        );

        return back()->with('success', 'ปฏิเสธใบเสร็จเรียบร้อย');
    }
}
