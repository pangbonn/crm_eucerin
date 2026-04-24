<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Zone;
use App\Models\ShopType;
use App\Models\Province;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function index(Request $request)
    {
        $query = Branch::with(['zone', 'shopType', 'province'])
            ->join('zones',      'branches.zone_id',      '=', 'zones.id')
            ->join('shop_types', 'branches.shop_type_id', '=', 'shop_types.id')
            ->select('branches.*');

        if ($request->filled('zone'))      $query->where('branches.zone_id',      $request->zone);
        if ($request->filled('shop_type')) $query->where('branches.shop_type_id', $request->shop_type);
        if ($request->filled('search'))    $query->where('branches.shop_name', 'like', '%' . $request->search . '%');

        $branches  = $query->orderBy('zones.name')->orderBy('branches.shop_name')->paginate(20)->withQueryString();
        $zones     = Zone::orderBy('name')->get();
        $shopTypes = ShopType::orderBy('name')->get();

        return view('admin.branches.index', compact('branches', 'zones', 'shopTypes'));
    }

    public function create()
    {
        return view('admin.branches.form', [
            'branch'    => new Branch,
            'zones'     => Zone::orderBy('name')->get(),
            'shopTypes' => ShopType::orderBy('name')->get(),
            'provinces' => Province::orderBy('name_th')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'zone_id'     => 'required|exists:zones,id',
            'shop_type_id'=> 'required|exists:shop_types,id',
            'province_id' => 'nullable|exists:provinces,id',
            'shop_name'   => 'required|string|max:200',
        ]);

        Branch::create($data);

        return redirect()->route('admin.branches.index')->with('success', 'เพิ่มสาขาเรียบร้อย');
    }

    public function edit(Branch $branch)
    {
        return view('admin.branches.form', [
            'branch'    => $branch,
            'zones'     => Zone::orderBy('name')->get(),
            'shopTypes' => ShopType::orderBy('name')->get(),
            'provinces' => Province::orderBy('name_th')->get(),
        ]);
    }

    public function update(Request $request, Branch $branch)
    {
        $data = $request->validate([
            'zone_id'     => 'required|exists:zones,id',
            'shop_type_id'=> 'required|exists:shop_types,id',
            'province_id' => 'nullable|exists:provinces,id',
            'shop_name'   => 'required|string|max:200',
            'is_active'   => 'boolean',
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
