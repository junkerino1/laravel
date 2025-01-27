<?php

namespace App\Http\Controllers;

use App\Services\SmsService;
use Illuminate\Http\Request;
use Laravel\Ui\Presets\React;
use App\Models\Otp;
use Illuminate\Support\Facades\DB;
use App\Mail\WeeklyReport;
use Illuminate\Support\Facades\Mail;

class SmsController extends Controller
{
    protected $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    public function send(Request $request)
    {
        $request->validate([
            'to' => 'required|string',
        ]);

        $otp = $this->generateOTP();

        $to = $request->to;
        $message = 'Your OTP is ' . $otp . '. Valid for 5 minutes.';

        $response = $this->smsService->sendSms($to, $message);

        return redirect('/otp')->with(['message' => 'OTP sent successfully.']);
    }

    public function generateOTP()
    {
        $user_id = auth()->id();
        $randomOTP = rand(100000, 999999);

        Otp::create([
            'user_id' => $user_id,
            'otp' => $randomOTP,
            'expires_at' => now()->addMinutes(5),
        ]);

        return $randomOTP;
    }

    public function checkOTP(Request $request)
    {
        $user_id = auth()->id();

        $request->validate([
            'otp' => 'required|integer',
        ]);

        $otpRecord = Otp::where('user_id', $user_id)->where('expires_at', '>=', now())->latest()->first();

        if (!$otpRecord) {
            return response()->json(['error' => 'OTP expired or invalid'], 400);
        }

        if ($request->otp == $otpRecord->otp) {
            $delete = Otp::where('otp','=', $request->otp);
            $delete->delete();
            return response()->json(['success' => 'OTP verified successfully']);
        }

        return response()->json(['error' => 'Invalid OTP'], 400);
    }


}
