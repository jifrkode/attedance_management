<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\MailTestController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DebugController;
use Illuminate\Support\Facades\URL;
use App\Models\User;

// ホームページルート
Route::get('/', function () {
    return Auth::check()
        ? redirect()->route('attendance.index')
        : redirect()->route('login');
})->name('home');

// 認証関連ルート
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// 認証が不要なルート
Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])
    ->middleware(['signed']) // 署名付きURLのみ許可
    ->name('verification.verify');

// ログイン不要でメール再送信
Route::post('/email/resend', [VerificationController::class, 'resend'])
    ->middleware('throttle:6,1') // 再送信の制限
    ->name('verification.resend');

// ログインが必要なルート
// Route::middleware('auth')->group(function () {
//     Route::get('/email/verify', [VerificationController::class, 'show'])->name('verification.notice');
// });

// メール送信確認画面
Route::get('/email-sent', fn() => view('auth.email-sent'))->name('email.sent');
// メールテスト
Route::get('/email-test', [VerificationController::class, 'show'])->name('verification.notice');
// Route::get('/test-email', function () {
//     $user = App\Models\User::first();
//     $user->sendEmailVerificationNotification();

//     return 'Test email sent!';
// });
// Route::get('/email-test', [MailTestController::class, 'sendTestEmail'])->name('sendTestEmail');

// 勤怠管理ルート
Route::middleware('auth')->prefix('attendance')->name('attendance.')->group(function () {
    Route::get('/', [AttendanceController::class, 'index'])->name('index');
    Route::get('/dayslist', [AttendanceController::class, 'dayslist'])->name('dayslist');
    Route::post('/start', [AttendanceController::class, 'start'])->name('start');
    Route::post('/end', [AttendanceController::class, 'end'])->name('end');
    Route::post('/break/start', [AttendanceController::class, 'startBreak'])->name('startBreak');
    Route::post('/break/end', [AttendanceController::class, 'endBreak'])->name('endBreak');

    // 管理者のみアクセス可能
    Route::middleware('can:manage-users')->group(function () {
        Route::get('/userlist', [AttendanceController::class, 'userslist'])->name('userslist');
    });

    // デバッグ用認証バイパスルート
    Route::get('/force-login/{id}', function ($id) {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        Auth::login($user);

        return response()->json(['message' => 'ログイン成功', 'user' => $user]);
    });
});
