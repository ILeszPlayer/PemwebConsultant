<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CounselingSession;
use App\Models\ChatMessage;
use App\Services\HexaAIService;

class ChatController extends Controller
{
    protected HexaAIService $hexaAI;

    public function __construct(HexaAIService $hexaAI)
    {
        $this->hexaAI = $hexaAI;
    }

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

        $aiResult = $this->hexaAI->generate($session, $request->message);

        ChatMessage::create([
            'counseling_session_id' => $session->id,
            'user_id' => null,
            'sender' => 'ai',
            'message' => $aiResult['text'],
        ]);

        return redirect()->route('sessions.show', $session)
            ->with('last_emotion', $aiResult['emotion'])
            ->with('widget_type', $aiResult['widget_type']);
    }
}
