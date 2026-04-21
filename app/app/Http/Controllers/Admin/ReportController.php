<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Exports\UsersExport;
use App\Exports\PointsExport;
use App\Exports\RedemptionsExport;
use App\Exports\ExamResultsExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index()
    {
        return view('admin.reports.index');
    }

    public function export(Request $request, string $type)
    {
        $month = $request->get('month');
        $year  = $request->get('year');

        switch ($type) {
            case 'users':
                return Excel::download(new UsersExport, 'employees_' . now()->format('Ymd') . '.xlsx');
            case 'points':
                return Excel::download(new PointsExport, 'points_' . now()->format('Ymd') . '.xlsx');
            case 'redemptions':
                return Excel::download(new RedemptionsExport($month, $year), 'redemptions_' . now()->format('Ymd') . '.xlsx');
            case 'exam-results':
                return Excel::download(new ExamResultsExport, 'exam_results_' . now()->format('Ymd') . '.xlsx');
            default:
                return back()->with('error', 'ประเภทรายงานไม่ถูกต้อง');
        }
    }
}
