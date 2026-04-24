<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Point;
use App\Models\User;
use Illuminate\Http\Request;

class PointController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $points = Point::where('user_id', $user->id)
            ->latest()
            ->paginate(20);

        return response()->json($points);
    }

    public function ranking()
    {
        $users = User::where('is_active', true)
            ->withSum(['points as ranking_points' => function ($q) {
                $q->where('source', '!=', 'redemption')
                  ->where('points', '>', 0);
            }], 'points')
            ->orderByDesc('ranking_points')
            ->limit(10)
            ->get(['id', 'name', 'lastname', 'level', 'photo_url']);

        return response()->json($users->map(function ($u, $i) {
            return [
                'rank'         => $i + 1,
                'name'         => $u->name . ' ' . $u->lastname,
                'level'        => $u->level,
                'photo_url'    => $u->photo_url,
                'total_points' => (int) ($u->ranking_points ?: 0),
            ];
        }));
    }
}
