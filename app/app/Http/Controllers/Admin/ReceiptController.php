<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Receipt;
use App\Exports\ReceiptProductsExport;
use App\Services\PointCalculationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

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
        $query = Receipt::with('user.currentBranch.branch.zone')->orderByDesc('created_at');

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
        $receipt->load(['user.currentBranch.branch.zone', 'pointHistory', 'approver']);
        return view('admin.receipts.show', compact('receipt'));
    }

    public function approve(Request $request, Receipt $receipt)
    {
        if ($receipt->status === 'rejected') {
            return back()->with('error', 'ใบเสร็จที่ถูกปฏิเสธไม่สามารถแก้ไขได้');
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

    public function cancel(Request $request, Receipt $receipt)
    {
        if ($receipt->status !== 'approved') {
            return back()->with('error', 'ยกเลิกได้เฉพาะใบเสร็จที่อนุมัติแล้วเท่านั้น');
        }

        $request->validate(['note' => 'required|string|max:500']);

        $this->pointService->cancelReceiptPoints($receipt, Auth::guard('admin')->user());

        $receipt->update(['note' => $request->note]);

        return back()->with('success', 'ยกเลิกการอนุมัติและหักคะแนนคืนเรียบร้อย');
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

    public function exportProducts()
    {
        return Excel::download(new ReceiptProductsExport, 'receipt_products_' . now()->format('Ymd_His') . '.xlsx');
    }
}
