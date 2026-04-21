<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Receipt;
use App\Models\RewardRedemption;
use App\Models\Point;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'users_active'       => User::where('is_active', true)->count(),
            'users_resigned'     => User::where('is_active', false)->count(),
            'receipts_pending'   => Receipt::where('status', 'pending')->count(),
            'redemptions_pending'=> RewardRedemption::where('status', 'pending')->count(),
            'total_points'       => Point::sum('points'),
        ];

        $recentUsers    = User::with('currentBranch.branch')->latest()->limit(10)->get();
        $recentReceipts = Receipt::with('user')->where('status', 'pending')->latest()->limit(5)->get();

        return view('admin.dashboard', compact('stats', 'recentUsers', 'recentReceipts'));
    }
}
