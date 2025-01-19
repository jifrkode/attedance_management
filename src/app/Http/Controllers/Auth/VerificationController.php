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

    public function resend(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $email = $request->input('email');
        $user = User::where('email', $email)->first();

        if (!$user) {
            Log::error('User not found for resend:', ['email' => $email]);
            return back()->withErrors('このメールアドレスは登録されていません。');
        }

        if ($user->hasVerifiedEmail()) {
            Log::info('User already verified, no email sent:', ['email' => $email]);
            return back()->with('status', 'すでに認証済みです。');
        }

        try {
            // ログの追加
            Log::info('Sending verification email:', ['email' => $email]);

            $user->sendEmailVerificationNotification();

            Log::info('Verification email sent successfully:', ['email' => $email]);
        } catch (\Exception $e) {
            Log::error('Failed to resend verification email:', [
                'email' => $email,
                'error' => $e->getMessage(),
            ]);
            return back()->withErrors('認証メールの再送信に失敗しました。');
        }

        return redirect('/login')->with('status', '認証メールを再送信しました。');
    }
}
