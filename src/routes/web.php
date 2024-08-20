<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\HomeController;

// ホームページルート
Route::get('/', function () {
    return view('welcome'); // 例えば、ホームページのビューを表示
})->name('home');

// 会員登録ページ
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

// ログインページ
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);

// ログアウト処理
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// 打刻ページ
Route::middleware('auth')->group(function () {
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/attendance/start', [AttendanceController::class, 'start'])->name('attendance.start');
    Route::post('/attendance/end', [AttendanceController::class, 'end'])->name('attendance.end');
    Route::post('/attendance/break/start', [AttendanceController::class, 'startBreak'])->name('attendance.startBreak');
    Route::post('/attendance/break/end', [AttendanceController::class, 'endBreak'])->name('attendance.endBreak');
    Route::get('/attendance/{date}', [AttendanceController::class, 'showByDate'])->name('attendance.showByDate');
});

// ユーザーページ（権限のあるユーザーのみアクセス可能）
Route::middleware('auth', 'can:manage-users')->group(function () {
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
});
