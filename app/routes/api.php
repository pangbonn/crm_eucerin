<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Api\ReceiptController;
use App\Http\Controllers\Api\PointController;
use App\Http\Controllers\Api\ExamController;
use App\Http\Controllers\Api\RewardController;
use App\Http\Controllers\Api\BannerController;
use App\Http\Controllers\Api\QAController;

// Public — ไม่ต้อง auth
Route::prefix('liff')->name('liff.')->group(function () {
    Route::post('login',    [AuthController::class, 'login'])->name('login');
    Route::post('register', [AuthController::class, 'register'])->name('register');

    // Location (ใช้ตอน register ก่อน login)
    Route::get('provinces',              [LocationController::class, 'provinces']);
    Route::get('districts/{provinceId}', [LocationController::class, 'districts']);
    Route::get('subdistricts/{districtId}', [LocationController::class, 'subdistricts']);
    Route::get('branches',               [LocationController::class, 'branches']);

    // Banner (public — ไม่ต้อง login ก็ดูได้)
    Route::get('banner/{type}', [BannerController::class, 'show']);
});

// Protected — ต้อง JWT
Route::prefix('liff')->name('liff.')->middleware('auth:api')->group(function () {
    Route::get('me', [AuthController::class, 'me']);

    // คะแนน
    Route::get('points',   [PointController::class, 'index']);
    Route::get('ranking',  [PointController::class, 'ranking']);

    // ใบเสร็จ
    Route::post('receipts', [ReceiptController::class, 'store']);

    // Exam
    Route::get('exams',                           [ExamController::class, 'index']);
    Route::get('exams/{exam}/questions',          [ExamController::class, 'questions']);
    Route::post('exams/{exam}/submit',            [ExamController::class, 'submit']);
    Route::get('exam-results',                    [ExamController::class, 'myResults']);

    // Rewards
    Route::get('rewards',     [RewardController::class, 'index']);
    Route::post('redeem',     [RewardController::class, 'redeem']);
    Route::get('redemptions', [RewardController::class, 'myRedemptions']);

    // Q&A
    Route::get('qa', [QAController::class, 'index']);
});
