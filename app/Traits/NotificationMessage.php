<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\ConnectionException;

trait NotificationMessage
{
    public function sendWhatsapp($url, $recipientNumber, $templateName, $params)
    {
        if (!config('services.wappin.enabled')) {
            return ['status' => false, 'message' => 'Notifikasi WhatsApp dinonaktifkan.'];
        }

        $username = config('services.wappin.username');
        $password = config('services.wappin.password');
        try {
            $headers = [
                'Content-Type' => 'application/json',
            ];

            $loginResponse = Http::withHeaders($headers)
                ->retry(3, 100, function ($exception, $request) {
                    return true;
                })
                ->withBasicAuth($username, $password)
                ->post('https://api.chat.wappin.app/v1/users/login');

            $loginData = $loginResponse->json();

            if (!isset($loginData['users'][0]['token'])) {
                Log::channel('wappin')->error('Token Wappin tidak ditemukan dalam response.');
                return ['status' => false, 'message' => 'Token tidak ditemukan.'];
            }

            $token = $loginData['users'][0]['token'];

            // Prepare payload
            $body = array_map(fn($value) => [
                "type" => "text",
                "text" => $value
            ], $params);

            $components = [
                [
                    "type" => "body",
                    "parameters" => $body
                ]
            ];

            // Tambah button URL jika ada
            if (!empty($url) && $url != "-") {
                $components[] = [
                    "type" => "button",
                    "sub_type" => "url",
                    "index" => "0",
                    "parameters" => [
                        ["type" => "text", "text" => $url]
                    ]
                ];
            }

            // Tambah button copy_code untuk template Authentication (OTP)
            // Tombol "Salin Kode" butuh parameter copy_code dengan nilai OTP
            if (!empty($params[0]) && $templateName === 'rekrutmen_informasi_send_otp') {
                $components[] = [
                    "type" => "button",
                    "sub_type" => "copy_code",
                    "index" => "0",
                    "parameters" => [
                        ["type" => "coupon_code", "coupon_code" => $params[0]]
                    ]
                ];
            }

            $post = [
                "to" => preg_replace('/^(?:\+?62|0)?/', '+62', $recipientNumber),
                "type" => "template",
                "template" => [
                    "name" => $templateName,
                    "language" => [
                        "policy" => "deterministic",
                        "code" => "id"
                    ],
                    "components" => $components
                ]
            ];

            // Kirim pesan WA dengan retry
            $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $token,
                    'Content-Type' => 'application/json'
                ])
                ->retry(3, 100, function ($exception, $request) {
                    return true;
                })
                ->post('https://api.chat.wappin.app/v1/messages', $post);

                Log::channel('wappin')->info('Response kirim WA', [
    'to' => $recipientNumber,
    'template' => $templateName,
    'status_code' => $response->status(),
    'body' => $response->json(),
]);

            if ($response->failed()) {
                Log::channel('wappin')->error('Gagal mengirim WA', [
                    'to' => $recipientNumber,
                    'ticketNumber' => $params[0],
                    'template' => $templateName,
                    'response' => $response->body()
                ]);

                return ['status' => false, 'message' => 'Gagal mengirim notifikasi WA.'];
            }

            return [
                'status' => true,
                'data' => $response->json(),
            ];

        } catch (\Exception $e) {
            Log::channel('wappin')->error('Error saat mengirim WhatsApp', [
                'error' => $e->getMessage(),
                'to' => $recipientNumber,
                'ticketNumber' => $params[0],
                'template' => $templateName,
            ]);

            return ['status' => false, 'message' => 'Terjadi kesalahan saat mengirim notifikasi.'];
        }
    }

}
