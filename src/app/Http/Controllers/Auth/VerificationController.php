<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Log;

class VerificationController extends Controller
{
    // メール認証ページの表示
    public function show()
    {
        return view('auth.verify-email');
    }

    public function verify(EmailVerificationRequest $request)
    {
        Log::info('認証クリック');
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            Log::info('既に認証済み', ['user_id' => $user->id]);
            return Redirect::route('home')->with('status', 'メールはすでに認証済みです。');
        }

        Log::info('認証開始', ['user_id' => $user->id]);

        // email_verified_at を更新
        $user->markEmailAsVerified();

        Log::info('認証完了', ['email_verified_at' => $user->email_verified_at]);

        return Redirect::route('home')->with('status', 'メールアドレスが認証されました。');
    }


    // 認証メールの再送信
    public function resend(Request $request)
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return Redirect::route('login')->with('status', 'すでに認証済みです。');
        }

        $user->sendEmailVerificationNotification();
        Log::info('After markEmailAsVerified', ['email_verified_at' => $user->email_verified_at]);
        return Redirect::route('verification.notice')->with('status', '認証メールを再送信しました。');
    }
}
