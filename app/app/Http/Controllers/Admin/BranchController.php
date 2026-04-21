<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Province;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function index(Request $request)
    {
        $query = Branch::with('province');

        if ($request->filled('zone'))      $query->where('zone', $request->zone);
        if ($request->filled('shop_type')) $query->where('shop_type', $request->shop_type);
        if ($request->filled('search')) {
            $query->where('shop_name', 'like', '%' . $request->search . '%');
        }

        $branches  = $query->orderBy('zone')->orderBy('shop_name')->paginate(20)->withQueryString();
        $zones     = Branch::zones();
        $shopTypes = Branch::shopTypes();

        return view('admin.branches.index', compact('branches', 'zones', 'shopTypes'));
    }

    public function create()
    {
        return view('admin.branches.form', [
            'branch'    => new Branch,
            'zones'     => Branch::zones(),
            'shopTypes' => Branch::shopTypes(),
            'provinces' => Province::orderBy('name_th')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'zone'       => 'required|in:' . implode(',', Branch::zones()),
            'province_id'=> 'nullable|exists:provinces,id',
            'shop_type'  => 'required|string|max:100',
            'shop_name'  => 'required|string|max:200',
        ]);

        Branch::create($data);

        return redirect()->route('admin.branches.index')->with('success', 'เพิ่มสาขาเรียบร้อย');
    }

    public function edit(Branch $branch)
    {
        return view('admin.branches.form', [
            'branch'    => $branch,
            'zones'     => Branch::zones(),
            'shopTypes' => Branch::shopTypes(),
            'provinces' => Province::orderBy('name_th')->get(),
        ]);
    }

    public function update(Request $request, Branch $branch)
    {
        $data = $request->validate([
            'zone'       => 'required|in:' . implode(',', Branch::zones()),
            'province_id'=> 'nullable|exists:provinces,id',
            'shop_type'  => 'required|string|max:100',
            'shop_name'  => 'required|string|max:200',
            'is_active'  => 'boolean',
        ]);

        $branch->update($data);

        return redirect()->route('admin.branches.index')->with('success', 'อัพเดทสาขาเรียบร้อย');
    }

    public function destroy(Branch $branch)
    {
        $branch->delete();
        return back()->with('success', 'ลบสาขาเรียบร้อย');
    }
}
