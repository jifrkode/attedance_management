<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request)
    {
        // バリデーション済みのメアドとパスワードのデータを取得
        $credentials = $request->only('email', 'password');

        // 認証を試みる
        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // ユーザーが MustVerifyEmail を実装していて、認証されていない場合
            if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail()) {
                Auth::logout(); // ログアウト

                // メール認証ページにリダイレクト
                return redirect()->route('verification.notice')->withErrors([
                    'email' => 'メールアドレスが未確認です。認証メールをご確認ください。',
                ]);
            }

            // 認証成功時にリダイレクト
            return redirect()->intended('attendance');
        }

        // 認証失敗時にエラーメッセージを渡してリダイレクト
        return redirect()->back()->withErrors([
            'email' => 'ログインに失敗しました。',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
