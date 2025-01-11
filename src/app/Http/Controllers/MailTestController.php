<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Mail;

class MailTestController extends Controller
{
    public function sendTestEmail()
    {
        try {
            Mail::raw('This is a test mail.', function($message) {
                $message->to('kazuk6114@gmail.com')
                        ->subject('Test Email');
            });

            return response('Email sent successfully!', 200);
        } catch (\Exception $e) {
            return response('Failed to send email: ' . $e->getMessage(), 500);
        }
    }
}
