<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class TestMailController extends Controller
{
    public function test()
{
    config([
        'mail.mailers.smtp' => [
            'transport' => 'smtp',
            'host' => 'smtp.gmail.com',
            'port' => 465,
            'encryption' => 'ssl',
            'username' =>'Shozuabadenis@gmail.com',
            'password' =>'ijlkjadjqfewatbk',
            'timeout' => null,
        ]
    ]);

    try {
        Mail::raw('🎉 Congratulations! Your Laravel Gmail setup is working perfectly!', function($message) {
            $message->to('your-email@gmail.com') // ← Your email
                    ->subject('✅ Gmail Setup Successful!');
        });
        return 'Email sent successfully! Check your inbox.';
    } catch (\Exception $e) {
        return 'Error: ' . $e->getMessage();
    }
}
}