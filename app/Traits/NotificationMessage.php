<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\ConnectionException;

trait NotificationMessage
{
    public function sendWhatsapp($url, $recipientNumber, $templateName, $params)
    {
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

            $post = [
                "to" => preg_replace('/^(?:\+?62|0)?/', '+62', $recipientNumber),
                "type" => "template",
                "template" => [
                    "name" => $templateName,
                    "language" => [
                        "policy" => "deterministic",
                        "code" => "id"
                    ],
                    "components" => [
                        [
                            "type" => "body",
                            "parameters" => $body
                        ]
                    ]
                ]
            ];

            if (!empty($url) && $url != "-") {
                $post['template']['components'][] = [
                    "type" => "button",
                    "sub_type" => "url",
                    "index" => "0",
                    "parameters" => [
                        ["type" => "text", "text" => $url]
                    ]
                ];
            }

            // Kirim pesan WA dengan retry
            $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $token,
                    'Content-Type' => 'application/json'
                ])
                ->retry(3, 100, function ($exception, $request) {
                    return true;
                })
                ->post('https://api.chat.wappin.app/v1/messages', $post);

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
