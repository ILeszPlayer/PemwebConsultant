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
            abort(403, 'Akses ilegal terdeteksi.');
        }

        $messages = ChatMessage::where('counseling_session_id', $session->id)
            ->orderBy('id', 'asc')
            ->get();

        return view('sessions.show', compact('session', 'messages'));
    }

    public function store(Request $request, CounselingSession $session)
    {
        if ($session->user_id !== Auth::id()) {
            abort(403, 'Otorisasi tidak valid.');
        }

        if ($session->status === 'finished') {
            return redirect()->back()->with('error', 'Sesi ini sudah resmi selesai diarsipkan.');
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

        $maxRetries = 3;
        $attempt = 0;
        $lastAiMessage = ChatMessage::where('counseling_session_id', $session->id)
            ->where('sender', 'ai')
            ->latest('id')
            ->value('message');

        do {
            $aiPayload = $this->aiService->generateResponse($session->id, $request->message);
            $attempt++;
        } while ($attempt < $maxRetries && $aiPayload['message'] === $lastAiMessage);

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

        $session->update([
            'status' => 'finished',
            'final_mood' => $request->final_mood,
            'is_escalated' => ($request->final_mood === 'need_doctor')
        ]);

        return redirect()->route('dashboard')->with('success', 'Sesi berhasil ditutup dengan aman.');
    }

    public function export(CounselingSession $session)
    {
        if ($session->user_id !== Auth::id() && auth()->user()->role !== 'doctor') {
            abort(403);
        }

        $messages = ChatMessage::where('counseling_session_id', $session->id)
            ->orderBy('id', 'asc')
            ->get();

        $text = "=== Ekspor Chat Hexa Space ===\n";
        $text .= "Judul: {$session->title}\n";
        $text .= "Tanggal: {$session->created_at->format('d M Y H:i')}\n";
        $text .= "Status: {$session->status}\n";
        $text .= str_repeat('=', 40) . "\n\n";

        foreach ($messages as $msg) {
            $sender = $msg->sender === 'user' ? 'Kamu' : 'Hexa AI';
            $time = $msg->created_at->format('d M Y H:i');
            $text .= "[{$time}] {$sender}:\n{$msg->message}\n\n";
        }

        $filename = 'chat-hexaspace-' . $session->id . '-' . now()->format('Ymd') . '.txt';

        return response($text, 200, [
            'Content-Type' => 'text/plain',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function destroyMessage(Request $request, ChatMessage $message)
    {
        $session = $message->counselingSession;

        if ($session->user_id !== Auth::id()) {
            abort(403);
        }

        if ($message->sender !== 'user') {
            return back()->with('error', 'Hanya bisa menghapus pesan sendiri.');
        }

        $message->delete();

        return back()->with('success', 'Pesan berhasil dihapus.');
    }
}
