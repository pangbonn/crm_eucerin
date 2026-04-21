<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\UserLevelImport;
use App\Exports\UsersExport;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['currentBranch.branch', 'address']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('lastname', 'like', "%{$s}%")
                  ->orWhere('employee_code', 'like', "%{$s}%")
                  ->orWhere('phone', 'like', "%{$s}%");
            });
        }

        if ($request->filled('level')) {
            $query->where('level', $request->level);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        if ($request->filled('zone')) {
            $query->whereHas('currentBranch.branch', fn($q) => $q->where('zone', $request->zone));
        }

        $users = $query->latest()->paginate(20)->withQueryString();

        return view('admin.users.index', [
            'users' => $users,
            'zones' => Branch::zones(),
        ]);
    }

    public function show(User $user)
    {
        $user->load(['address.province', 'address.district', 'address.subdistrict',
                     'currentBranch.branch.province',
                     'points' => fn($q) => $q->latest()->limit(20),
                     'redemptions.reward' => fn($q) => $q->latest(),
                    ]);

        $totalPoints = $user->points()->sum('points');

        return view('admin.users.show', compact('user', 'totalPoints'));
    }

    public function edit(User $user)
    {
        $user->load(['address', 'currentBranch']);
        $branches = Branch::where('is_active', true)->orderBy('zone')->orderBy('shop_name')->get();

        return view('admin.users.edit', compact('user', 'branches'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'          => 'required|string|max:100',
            'lastname'      => 'required|string|max:100',
            'phone'         => 'required|digits:10',
            'level'         => 'required|in:gold,silver,platinum',
            'employee_code' => 'required|string|unique:users,employee_code,' . $user->id,
        ]);

        $user->update($request->only('name', 'lastname', 'phone', 'level', 'employee_code'));

        return redirect()->route('admin.users.show', $user)->with('success', 'อัพเดทข้อมูลเรียบร้อย');
    }

    public function resign(User $user)
    {
        if (!$user->is_active) {
            return back()->with('error', 'พนักงานลาออกไปแล้ว');
        }

        $user->update([
            'is_active'   => false,
            'resigned_at' => now(),
        ]);

        return back()->with('success', "ระบบบันทึกการลาออกของ {$user->name} {$user->lastname} เรียบร้อย");
    }

    public function importLevel(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ]);

        Excel::import(new UserLevelImport, $request->file('file'));

        return back()->with('success', 'Import ระดับพนักงานเรียบร้อย');
    }

    public function export()
    {
        return Excel::download(new UsersExport, 'employees_' . now()->format('Ymd') . '.xlsx');
    }
}
