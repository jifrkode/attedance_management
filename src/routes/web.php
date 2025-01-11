<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\MailTestController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\VerificationController;
use Illuminate\Support\Facades\DB;

// ホームページルート
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('attendance.index');
    }
    return redirect()->route('login');
})->name('home');

// 会員登録ページ
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');

Route::middleware('auth')->group(function () {
    // メール認証確認用ルート
    Route::get('/email/verify', [VerificationController::class, 'show'])->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])->middleware('signed')->name('verification.verify');
    Route::post('/email/resend', [VerificationController::class, 'resend'])->middleware('throttle:6,1')->name('verification.resend');
});

Route::get('/email-sent', function () {
    return view('auth.email-sent');
})->name('email.sent');
Route::post('/register', [RegisterController::class, 'register']);

// ログインページ
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::middleware(['auth', 'verified'])->group(function () {
    // 認証済みユーザーのみがアクセスできるルート
    Route::get('/attendance', function () {
        return view('attendance.index');
    })->name('attendance');
});

// メール認証用ルート
Route::middleware(['auth'])->group(function () {
    Route::get('/email/verify', [VerificationController::class, 'show'])->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])
        ->middleware(['signed'])->name('verification.verify');
    Route::post('/email/resend', [VerificationController::class, 'resend'])
        ->middleware(['throttle:6,1'])->name('verification.resend');
        
});


// ログアウト処理
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// 打刻ページ
Route::middleware('auth')->group(function () {
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::middleware(['auth', 'can:manage-users'])->group(function () {
        Route::get('/attendance/usesrlist', [AttendanceController::class, 'userslist'])->name('attendance.userslist');
    });
    Route::get('/attendance/dayslist', [AttendanceController::class, 'dayslist'])->name('attendance.dayslist');
    Route::post('/attendance/start', [AttendanceController::class, 'start'])->name('attendance.start');
    Route::post('/attendance/end', [AttendanceController::class, 'end'])->name('attendance.end');
    Route::post('/attendance/break/start', [AttendanceController::class, 'startBreak'])->name('attendance.startBreak');
    Route::post('/attendance/break/end', [AttendanceController::class, 'endBreak'])->name('attendance.endBreak');
    // Route::get('/attendance/{date}', [AttendanceController::class, 'showByDate'])->name('attendance.showByDate');
});

// ユーザーページ（権限のあるユーザーのみアクセス可能）
// Route::middleware(['auth', 'can:manage-users'])->group(function () {
//     Route::get('/users', [UserController::class, 'index'])->name('users.admin');
//     Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
// });

// Route::get('/attendance/list', [AttendanceController::class, 'list'])->name('attendance.dayslist');

// テストメール送信
// Route::get('/send-test-email', [MailTestController::class, 'sendTestEmail']);

//本番デバック用
// Route::get('/db-check', function () {
//     try {
//         $tables = DB::select('SHOW TABLES');
//         DB::statement('SET FOREIGN_KEY_CHECKS=0;'); // 外部キー制約を無効化
//         DB::table('users')->truncate();           // users テーブルのデータを削除
//         DB::statement('SET FOREIGN_KEY_CHECKS=1;'); // 外部キー制約を再有効化

//         return response()->json($tables);
//     } catch (\Exception $e) {
//         return response()->json(['error' => $e->getMessage()]);
//     }
// });

// Route::get('/users', function () {
//     $users = DB::table('users')->get();
//     return response()->json($users);
// });
