<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\DB;

class RegisterController extends Controller
{
    /**
     * ユーザー登録フォームを表示
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * ユーザー登録処理
     */
    public function register(RegisterRequest $request)
    {
        try {
            // リクエストデータのバリデーション
            $validated = $request->validated();
            Log::info('Register request received.', ['data' => $validated]);

            // ユーザー作成
            $user = $this->create($validated);

            // メール認証通知を送信
            if ($user instanceof MustVerifyEmail) {
                try {
                    $user->sendEmailVerificationNotification();
                    Log::info('Email verification notification sent.', ['email' => $user->email]);
                } catch (\Exception $e) {
                    Log::error('Failed to send email verification: ' . $e->getMessage());
                    return redirect()->back()->withErrors(['error' => '認証メールの送信に失敗しました。']);
                }
            }

            // 登録完了後のリダイレクト
            return redirect()->route('email.sent')->with('success', '二段階認証メールを送信しました。');

        } catch (\Exception $e) {
            // 全体のエラーハンドリング
            Log::error('Registration failed: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => '登録中に問題が発生しました。もう一度お試しください。']);
        }
    }

    /**
     * ユーザー作成処理
     */
    protected function create(array $data)
    {
        try {
            return DB::transaction(function () use ($data) {
                $user = User::create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => Hash::make($data['password']),
                ]);

                Log::info('User created successfully.', [
                    'id' => $user->id,
                    'email' => $user->email,
                ]);

                return $user;
            });
        } catch (\Exception $e) {
            Log::error('User creation failed: ' . $e->getMessage());
            throw $e;
        }
    }
}
