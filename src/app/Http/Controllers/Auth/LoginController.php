<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    protected $redirectTo = '/attendance';

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail()) {
                Auth::logout();
                return redirect()->route('verification.notice')->withErrors([
                    'email' => 'メールアドレスが未確認です。認証メールをご確認ください。',
                ]);
            }

            Log::info('User logged in successfully.', ['user_id' => $user->id]);
            return redirect()->intended($this->redirectTo);
        }

        Log::warning('Failed login attempt.', ['email' => $request->email]);
        return redirect()->back()->withErrors([
            'email' => 'ログインに失敗しました。',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect(env('LOGOUT_REDIRECT', '/'));
    }
}
