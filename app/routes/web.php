<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin;

Route::get('/', fn() => redirect()->route('admin.login'));

// LIFF SPA — ทุก /liff/* ให้ Vue Router จัดการ
Route::get('/liff/{any?}', fn() => view('liff.index'))->where('any', '.*')->name('liff');

// Admin Auth (no middleware)
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('login',  [Admin\AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [Admin\AuthController::class, 'login'])->name('login.post');
    Route::post('logout',[Admin\AuthController::class, 'logout'])->name('logout');
});

// Admin Protected
Route::prefix('admin')->name('admin.')->middleware('admin.auth')->group(function () {
    Route::get('dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');

    // Users
    Route::resource('users', Admin\UserController::class)->except(['create', 'store']);
    Route::post('users/{user}/resign',          [Admin\UserController::class, 'resign'])->name('users.resign');
    Route::post('users/import-level',           [Admin\UserController::class, 'importLevel'])->name('users.import-level');
    Route::get('users/export',                  [Admin\UserController::class, 'export'])->name('users.export');

    // Branches
    Route::resource('branches', Admin\BranchController::class);

    // Receipts
    Route::get('receipts',                      [Admin\ReceiptController::class, 'index'])->name('receipts.index');
    Route::get('receipts/{receipt}',            [Admin\ReceiptController::class, 'show'])->name('receipts.show');
    Route::post('receipts/{receipt}/approve',   [Admin\ReceiptController::class, 'approve'])->name('receipts.approve');
    Route::post('receipts/{receipt}/reject',    [Admin\ReceiptController::class, 'reject'])->name('receipts.reject');

    // Points
    Route::get('points',                        [Admin\PointController::class, 'index'])->name('points.index');
    Route::get('points/{user}',                 [Admin\PointController::class, 'show'])->name('points.show');
    Route::post('points/{user}/adjust',         [Admin\PointController::class, 'adjust'])->name('points.adjust');
    Route::get('points/ranking',                [Admin\PointController::class, 'ranking'])->name('points.ranking');
    Route::get('points/export',                 [Admin\PointController::class, 'export'])->name('points.export');

    // Exams
    Route::resource('exams', Admin\ExamController::class);
    Route::resource('exams.questions', Admin\ExamQuestionController::class)->shallow();
    Route::get('exams/{exam}/results',          [Admin\ExamController::class, 'results'])->name('exams.results');

    // Rewards
    Route::resource('rewards', Admin\RewardController::class);
    Route::get('redemptions',                   [Admin\RedemptionController::class, 'index'])->name('redemptions.index');
    Route::post('redemptions/{redemption}/approve', [Admin\RedemptionController::class, 'approve'])->name('redemptions.approve');
    Route::post('redemptions/{redemption}/reject',  [Admin\RedemptionController::class, 'reject'])->name('redemptions.reject');
    Route::get('redemptions/export',            [Admin\RedemptionController::class, 'export'])->name('redemptions.export');

    // Banners
    Route::resource('banners', Admin\BannerController::class)->except(['show']);

    // Q&A
    Route::resource('qa-categories', Admin\QACategoryController::class);
    Route::resource('qa-items', Admin\QAItemController::class);

    // Reports
    Route::get('reports',                       [Admin\ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/export/{type}',         [Admin\ReportController::class, 'export'])->name('reports.export');
});
