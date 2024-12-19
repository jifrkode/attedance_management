<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class MailTestController extends Controller
{
    public function sendTestEmail()
    {
        $details = [
            'title' => 'Test Mail',
            'body' => 'This is a test mail.'
        ];

        Mail::raw('This is a test mail.', function($message) {
            $message->to('kazuk6114@gmail.com')
                    ->subject('Test Email');
        });

        return 'Email sent successfully!';
    }
}