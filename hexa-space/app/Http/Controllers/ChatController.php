<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CounselingSession;
use App\Models\ChatMessage;

class ChatController extends Controller
{
    public function store(Request $request, CounselingSession $session)
    {
        if ($session->user_id !== auth()->id()) {
            abort(403, 'Kamu tidak memiliki akses ke sesi ini.');
        }

        if ($session->status === 'finished') {
            return back()->with('error', 'Sesi ini sudah berakhir. Tidak dapat mengirim pesan baru.');
        }

        $request->validate([
            'message' => 'required|string|min:1|max:1000',
        ]);

        ChatMessage::create([
            'counseling_session_id' => $session->id,
            'user_id' => auth()->id(),
            'sender' => 'user',
            'message' => $request->message,
        ]);

        $aiResponse = $this->generateAiResponse($request->message);

        ChatMessage::create([
            'counseling_session_id' => $session->id,
            'user_id' => null,
            'sender' => 'ai',
            'message' => $aiResponse,
        ]);

        return redirect()->route('sessions.show', $session);
    }

    private function generateAiResponse(string $message): string
    {
        $lower = strtolower($message);

        if (str_contains($lower, 'sedih')) {
            return 'Aku turut merasakan kesedihan yang sedang kamu alami. Enggak apa-apa kok untuk merasa sedih, itu bagian dari proses manusiawi. Kalau kamu mau, coba ceritakan apa yang membuatmu merasa seperti itu? Kadang, meluapkan perasaan bisa membuat hati terasa lebih ringan.';
        }

        if (str_contains($lower, 'cemas') || str_contains($lower, 'takut')) {
            return 'Rasa cemas dan takut itu wajar, apalagi kalau kita menghadapi hal yang belum pasti. Coba tarik napas perlahan... tarik... lalu hembuskan. Kamu enggak sendirian, aku di sini buat dengerin ceritamu. Coba ceritakan apa yang membuatmu merasa cemas, ya?';
        }

        if (str_contains($lower, 'capek') || str_contains($lower, 'lelah')) {
            return 'Halo, aku dengar kamu merasa lelah. Itu sangat manusiawi, dan penting buat kamu untuk memberi izin pada diri sendiri buat istirahat. Coba luangkan sejenak buat sekadar minum air atau tarik napas dulu. Kalau ada yang mau diceritain, silakan ya, aku siap mendengarkan.';
        }

        return 'Terima kasih sudah mau berbagi cerita. Aku di sini untuk menemani kamu, apa pun yang sedang kamu rasakan. Coba ceritakan lebih lanjut, aku siap mendengarkan dengan saksama. Ingat, kamu tidak sendiri.';
    }
}
