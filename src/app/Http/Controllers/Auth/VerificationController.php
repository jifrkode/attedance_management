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

    // メール認証リンクを処理
    public function verify(Request $request)
    {
        // リンクからユーザーIDとハッシュを取得
        $id = $request->route('id');
        $hash = $request->route('hash');

        Log::info('Verification request received:', [
            'id' => $id,
            'hash' => $hash,
            'expires' => $request->query('expires'),
            'signature' => $request->query('signature'),
        ]);

        // ユーザーを明示的に取得
        $user = User::find($id);

        if (!$user) {
            // ユーザーが見つからない場合
            Log::error('User not found for verification:', ['id' => $id]);
            return redirect('/login')->withErrors('Invalid verification link.');
        }

        // ハッシュを検証
        if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            // ハッシュが一致しない場合
            Log::error('Hash mismatch for verification:', [
                'id' => $id,
                'expected_hash' => sha1($user->getEmailForVerification()),
                'provided_hash' => $hash,
                'email' => $user->getEmailForVerification(),
            ]);
            return redirect('/login')->withErrors('Invalid verification link.');
        }

        // 既に認証済みの場合
        if ($user->hasVerifiedEmail()) {
            Log::info('User already verified:', ['id' => $id]);
            return redirect('/login')->with('message', 'Email is already verified.');
        }

        // メール認証を完了
        $user->markEmailAsVerified();

        Log::info('Email verified successfully:', ['id' => $id]);
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
