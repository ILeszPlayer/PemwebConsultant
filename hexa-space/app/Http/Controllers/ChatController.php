<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CounselingSession;
use App\Models\ChatMessage;
use App\Services\HexaAIService;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    protected HexaAIService $aiService;

    public function __construct(HexaAIService $aiService)
    {
        $this->aiService = $aiService;
    }

    public function show(CounselingSession $session)
    {
        if ($session->user_id !== Auth::id()) {
            abort(403, 'Kamu tidak memiliki hak akses untuk membuka ruang aman ini.');
        }

        $messages = ChatMessage::where('counseling_session_id', $session->id)
            ->orderBy('id', 'asc')
            ->get();

        return view('sessions.show', compact('session', 'messages'));
    }

    public function store(Request $request, CounselingSession $session)
    {
        if ($session->user_id !== Auth::id()) {
            abort(403, 'Aksi ilegal terdeteksi.');
        }

        if ($session->status === 'finished') {
            return redirect()->back()->with('error', 'Sesi bercerita ini sudah diarsipkan.');
        }

        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        ChatMessage::create([
            'counseling_session_id' => $session->id,
            'user_id' => Auth::id(),
            'sender' => 'user',
            'message' => $request->message,
        ]);

        $aiPayload = $this->aiService->generateResponse($session->id, $request->message);

        ChatMessage::create([
            'counseling_session_id' => $session->id,
            'user_id' => null,
            'sender' => 'ai',
            'message' => $aiPayload['message'],
        ]);

        return redirect()->route('sessions.show', $session->id);
    }

    public function finish(Request $request, CounselingSession $session)
    {
        if ($session->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'final_mood' => 'required|string',
        ]);

        $isEscalated = ($request->final_mood === 'need_doctor');

        $session->update([
            'status' => 'finished',
            'final_mood' => $request->final_mood,
            'is_escalated' => $isEscalated
        ]);

        return redirect()->route('dashboard')->with('success', 'Sesi berhasil diarsipkan.');
    }
}
