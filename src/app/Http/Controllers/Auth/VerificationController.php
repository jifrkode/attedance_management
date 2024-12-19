<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;

class VerificationController extends Controller
{
    public function show()
    {
        return view('auth.verify-email');
    }

    public function verify(EmailVerificationRequest $request)
    {
        $request->fulfill();

        return Redirect::route('home')->with('verified', true);
    }

    public function resend(Request $request)
    {
        $request->user()->sendEmailVerificationNotification();

        return Redirect::route('verification.notice')->with('resent', true);
    }
}
