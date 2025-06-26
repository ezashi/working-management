<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\AdminAttendanceController;
use App\Http\Controllers\ModificationRequestController;

Route::get('/', function () {
    if (Auth::check()) {
        if (Auth::user()->isAdmin()) {
            return redirect()->route('admin.attendance');
        } else {
            return redirect()->route('attendance');
        }
    }
    return redirect()->route('login');
});

Route::middleware(['auth'])->group(function () {
    // 勤怠登録画面
    Route::get('/attendance', [AttendanceController::class, 'attendance'])->name('attendance');
    Route::post('/attendance/check-in', [AttendanceController::class, 'checkIn'])->name('attendance.check-in');
    Route::post('/attendance/check-out', [AttendanceController::class, 'checkOut'])->name('attendance.check-out');
    Route::post('/attendance/break-start', [AttendanceController::class, 'breakStart'])->name('attendance.break-start');
    Route::post('/attendance/break-end', [AttendanceController::class, 'breakEnd'])->name('attendance.break-end');

    // 勤怠一覧画面
    Route::get('/attendance/list', [AttendanceController::class, 'list'])->name('attendance.list');

    // 勤怠詳細画面
    Route::get('/attendance/{id}', [AttendanceController::class, 'show'])->name('attendance.show')->middleware('route.admin');
    Route::put('/attendance/{id}', [AttendanceController::class, 'update'])->name('attendance.update')->middleware('route.admin');

    // 申請画面
    Route::get('/stamp_correction_request/list', [ModificationRequestController::class, 'index'])->name('correction.request.index');
    Route::get('/stamp_correction_request/approval/{id}', [ModificationRequestController::class, 'showApproval'])->name('correction.request.approval.show');
});

// 管理者ログイン
Route::get('/admin/login', [AdminLoginController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AdminLoginController::class, 'login'])->name('admin.login.post');
Route::post('/admin/logout', [AdminLoginController::class, 'logout'])->name('admin.logout');

Route::middleware(['auth', 'admin'])->group(function () {
    // 勤怠一覧画面(管理者)
    Route::get('/admin/attendance/list', [AdminAttendanceController::class, 'attendance'])->name('admin.attendance');

    // スタッフ一覧画面
    Route::get('/admin/staff/list', [AdminAttendanceController::class, 'staff'])->name('admin.staff');

    // スタッフ別勤怠一覧画面
    Route::get('/admin/attendance/staff/{id}', [AdminAttendanceController::class, 'list'])->name('admin.list');

    // 修正申請承認画面
    Route::get('/stamp_correction_request/approve/{attendance_correct_request}', [ModificationRequestController::class, 'show'])->name('modification.request.show');
    Route::post('/stamp_correction_request/approve/{attendance_correct_request}', [ModificationRequestController::class, 'approval'])->name('modification.request.approval');
});