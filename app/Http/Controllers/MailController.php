<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\WeeklyReport;

class MailController extends Controller
{
    public function sendMail()
    {
        Mail::to('jkedgetech@gmail.com')->send(new WeeklyReport());
        return 'Test email sent!';
    }
}
