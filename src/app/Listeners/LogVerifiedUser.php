<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Log;

class LogVerifiedUser
{
    /**
     * Handle the event.
     *
     * @param  \Illuminate\Auth\Events\Verified  $event
     * @return void
     */
    public function handle(Verified $event)
    {
        // ログにユーザー情報を出力
        Log::info('User verified their email: ' . $event->user->email);

        // 必要であれば追加の処理を記述
    }
}
