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
