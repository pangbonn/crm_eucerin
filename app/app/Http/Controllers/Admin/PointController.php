<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Point;
use App\Models\Ranking;
use App\Services\PointCalculationService;
use App\Exports\PointsExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class PointController extends Controller
{
    private PointCalculationService $pointService;

    public function __construct(PointCalculationService $pointService)
    {
        $this->pointService = $pointService;
    }

    public function index(Request $request)
    {
        $query = User::withSum('points', 'points')
                     ->with('currentBranch.branch')
                     ->where('is_active', true);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('name','like',"%$s%")
                                      ->orWhere('employee_code','like',"%$s%"));
        }

        $users = $query->orderByDesc('points_sum_points')->paginate(20)->withQueryString();

        return view('admin.points.index', compact('users'));
    }

    public function show(User $user)
    {
        $points      = $user->points()->latest()->paginate(30);
        $totalPoints = $user->points()->sum('points');

        return view('admin.points.show', compact('user', 'points', 'totalPoints'));
    }

    public function adjust(Request $request, User $user)
    {
        $request->validate([
            'points' => 'required|integer|not_in:0',
            'note'   => 'required|string|max:200',
        ]);

        $this->pointService->adjustPoints(
            $user,
            (int)$request->points,
            $request->note,
            Auth::guard('admin')->user()
        );

        return back()->with('success', 'ปรับคะแนนเรียบร้อย');
    }

    public function ranking(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year  = $request->get('year',  now()->year);

        $rankings = Ranking::with('user')
            ->where('period_month', $month)
            ->where('period_year',  $year)
            ->orderBy('rank')
            ->limit(10)
            ->get();

        // ถ้าไม่มี snapshot ให้คำนวณจาก points สด
        if ($rankings->isEmpty()) {
            $rankings = User::withSum('points', 'points')
                ->where('is_active', true)
                ->orderByDesc('points_sum_points')
                ->limit(10)
                ->get()
                ->map(function ($u, $i) {
                    $u->rank         = $i + 1;
                    $u->total_points = $u->points_sum_points ?? 0;
                    return $u;
                });
        }

        return view('admin.points.ranking', compact('rankings', 'month', 'year'));
    }

    public function export()
    {
        return Excel::download(new PointsExport, 'points_' . now()->format('Ymd') . '.xlsx');
    }
}
