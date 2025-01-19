<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class VerificationController extends Controller
{
    // メール認証ページの表示
    public function show()
    {
        return view('auth.verify-email');
    }

    public function verify(EmailVerificationRequest $request)
    {
        // リクエストからユーザーIDとハッシュを取得
        $id = $request->route('id');
        $hash = $request->route('hash');

        // デバッグ情報をログに記録（必要に応じて有効化）
        Log::info('Verification request received:', ['id' => $id, 'hash' => $hash]);

        // ユーザーを取得
        $user = User::find($id);

        if (!$user) {
            // ユーザーが存在しない場合の処理
            Log::error('User not found for verification:', ['id' => $id]);
            return redirect('/login')->withErrors('Invalid verification link.');
        }

        // ハッシュを検証
        if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            // ハッシュが一致しない場合の処理
            Log::error('Hash mismatch for verification:', [
                'id' => $id,
                'expected_hash' => sha1($user->getEmailForVerification()),
                'provided_hash' => $hash,
                'email' => $user->getEmailForVerification(),
            ]);
            return redirect('/login')->withErrors('Invalid verification link.');
        }

        // すでに認証済みの場合
        if ($user->hasVerifiedEmail()) {
            Log::info('User already verified:', ['id' => $id]);
            return redirect('/dashboard')->with('message', 'Email is already verified.');
        }

        // 認証を完了
        $request->fulfill();

        // 成功メッセージを添えてリダイレクト
        Log::info('Email verification completed for user:', ['id' => $id]);
        return redirect('/dashboard')->with('message', 'Email verified successfully!');
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
