<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Log;

class VerificationController extends Controller
{
    // メール認証ページの表示
    public function show()
    {
        return view('auth.verify-email');
    }

    public function verify(EmailVerificationRequest $request)
    {
        Log::info('Verification request received', [
            'user_id' => $request->user()->id,
            'verified' => $request->user()->hasVerifiedEmail(),
        ]);
    
        if ($request->user()->hasVerifiedEmail()) {
            Log::info('Already verified');
            return redirect()->route('home')->with('status', 'Already verified');
        }
    
        Log::info('Marking email as verified');
        $request->fulfill();
        $request->user()->markEmailAsVerified();
    
        Log::info('Verification completed', [
            'user_id' => $request->user()->id,
            'verified_at' => $request->user()->email_verified_at,
        ]);
    
        return redirect()->route('home')->with('status', 'Email verified successfully');
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
