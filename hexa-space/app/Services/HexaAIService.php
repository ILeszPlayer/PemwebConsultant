<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\ChatMessage;

class HexaAIService
{
    protected string $apiKey;
    protected string $model;

    public function __construct()
    {
        $this->apiKey = env('OPENAI_API_KEY', '');
        $this->model = env('OPENAI_MODEL', 'gpt-4o-mini');
    }

    public function generateResponse(int $sessionId, string $userMessage): array
    {
        if (empty($this->apiKey)) {
            Log::error("OpenAI API Key belum dikonfigurasi di file .env");
            return [
                'message' => "Maaf ya, Kakak sedang mengalami sedikit kendala jaringan emosional. Bisa tolong kirim ulang pesanmu?",
                'widget_type' => 'none'
            ];
        }

        // Ambil 10 pesan terakhir untuk memelihara konteks (Multi-Turn Chat History Memory)
        $history = ChatMessage::where('counseling_session_id', $sessionId)
            ->orderBy('id', 'asc')
            ->take(10)
            ->get();

        $messages = [
            [
                'role' => 'system',
                'content' => "Nama kamu adalah Hexa AI, panggil dirimu 'Kakak' dan sapa pengguna dengan 'Kamu'. Kamu adalah konselor pendamping emosional awal di platform Hexa Space yang super hangat, tulus, penuh empati, dan taktis.
                ATURAN UTAMA JAWABAN:
                1. Kamu WAJIB membaca histori obrolan sebelumnya yang dikirimkan dalam payload request ini. JANGAN PERNAH mengulang-ulang kalimat pembuka yang sama atau memberikan jawaban template statis berulang! Berikan respon yang mengalir alami dan dinamis mengikuti alur chat terbaru.
                2. Jika pengguna menanyakan solusi konkret (seperti 'gimana cara ngomong ke ibu tentang judi/uang habis/ukt'), urai dalam langkah logis terstruktur (1, 2, 3) dan berikan contoh draf kalimat nyata yang santun untuk diucapkan kepada orang tua.
                3. Berikan validasi emosional terlebih dahulu, lalu bimbing mereka dengan bahasa Indonesia yang natural dan menenangkan."
            ]
        ];

        foreach ($history as $chat) {
            $role = ($chat->sender === 'user') ? 'user' : 'assistant';
            $messages[] = [
                'role' => $role,
                'content' => $chat->message
            ];
        }

        if (end($messages)['content'] !== $userMessage) {
            $messages[] = [
                'role' => 'user',
                'content' => $userMessage
            ];
        }

        try {
            $response = Http::withToken($this->apiKey)
                ->timeout(25)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => $this->model,
                    'messages' => $messages,
                    'temperature' => 0.7,
                ]);

            if ($response->successful()) {
                $result = $response->json();
                $aiReply = $result['choices'][0]['message']['content'] ?? '';

                if (!empty($aiReply)) {
                    $widgetType = 'none';
                    $cleanMessage = strtolower($userMessage);

                    if (str_contains($cleanMessage, 'judi') || str_contains($cleanMessage, 'judol') || str_contains($cleanMessage, 'depo') || str_contains($cleanMessage, 'gacor')) {
                        $widgetType = 'addiction_barrier';
                    } elseif (str_contains($cleanMessage, 'panik') || str_contains($cleanMessage, 'cemas') || str_contains($cleanMessage, 'deg-degan')) {
                        $widgetType = 'box_breathing';
                    }

                    return [
                        'message' => $aiReply,
                        'widget_type' => $widgetType
                    ];
                }
            }
            Log::error("OpenAI Response Error Status: " . $response->body());
        } catch (\Exception $e) {
            Log::error("OpenAI Service Exception: " . $e->getMessage());
        }

        return [
            'message' => "Kakak memahami situasi ini terasa berat bagimu. Mari kita urai dan cari solusinya bersama pelan-pelan ya. Ceritakan saja apa yang sedang mengganjal di hatimu.",
            'widget_type' => 'none'
        ];
    }
}
