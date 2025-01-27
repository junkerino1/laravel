<?php

namespace App\Services;

use bulk360\client;

class SmsService
{
    protected $smsClient;

    public function __construct()
    {
        // API Credentials
        $this->smsClient = new client('4openjEe7A', '2terFc4DDLZG2fQXgBeDFk15cYJqKHZCXZPnpJRJ');
    }

    public function sendSms($to, $message, $from = '68068')
    {
        try {
            $response = $this->smsClient->send([
                'from' => $from,
                'to' => $to,
                'text' => $message,
            ]);

            // Optionally decode the response if it's JSON
            $jsonResponse = json_decode($response);

            return $jsonResponse;
        } catch (\Exception $e) {
            // Handle any errors that may occur
            return ['error' => $e->getMessage()];
        }
    }
}
