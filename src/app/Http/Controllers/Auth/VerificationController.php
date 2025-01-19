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
        return redirect('/login')->with('message', 'Email verified successfully!');
    }

    // 認証メールの再送信
    public function resend(Request $request)
    {
        $user = $request->user();

        Log::info('Resend request received:', [
            'user_id' => $user->id,
            'email_verified' => $user->hasVerifiedEmail(),
            'email' => $user->email,
        ]);

        // ユーザーが既に認証済みの場合
        if ($user->hasVerifiedEmail()) {
            Log::info('User already verified, no email sent:', [
                'user_id' => $user->id,
                'email_verified_at' => $user->email_verified_at,
            ]);
            return Redirect::route('login')->with('status', 'すでに認証済みです。');
        }

        try {
            // 認証メールを再送信
            $user->sendEmailVerificationNotification();
            Log::info('Verification email resent successfully:', ['user_id' => $user->id]);
        } catch (\Exception $e) {
            // メール送信が失敗した場合
            Log::error('Failed to resend verification email:', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
            return Redirect::route('verification.notice')->withErrors('認証メールの再送信に失敗しました。もう一度お試しください。');
        }

        return Redirect::route('verification.notice')->with('status', '認証メールを再送信しました。');
    }
}
