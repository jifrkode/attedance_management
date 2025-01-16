<?php

namespace App\Http\Controllers;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class DebugController extends Controller
{
    /**
     * デバッグ用メール認証URLの生成と確認
     *
     * @return void
     */
    public function debugVerification()
    {
        // ログインしているユーザーを取得
        $user = Auth::user();

        if (!$user) {
            abort(403, 'ログインユーザーが必要です。');
        }

        // URLを生成
        $link = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(60),
            [
                'id' => $user->id,
                'hash' => sha1($user->email),
            ]
        );

        // 生成されたリンクを表示
        dd([
            'debug' => 'メール認証URLを生成しました。',
            'verification_url' => $link,
        ]);
    }
}
