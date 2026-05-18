<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CounselingSession;
use App\Models\CounselingService;
use App\Services\HexaAIService;

class CounselingSessionController extends Controller
{
    public function index()
    {
        $sessions = CounselingSession::where('user_id', auth()->id())
            ->latest()
            ->get();

        return view('sessions.index', compact('sessions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:counseling_services,id',
        ]);

        $service = CounselingService::findOrFail($request->service_id);

        if (!$service->is_active) {
            return back()->with('error', 'Maaf, layanan ini sedang tidak tersedia.');
        }

        $session = CounselingSession::create([
            'user_id' => auth()->id(),
            'counseling_service_id' => $service->id,
            'title' => 'Sesi ' . $service->name,
            'status' => 'active',
        ]);

        return redirect()->route('sessions.show', $session)
            ->with('success', 'Sesi baru telah dibuat. Selamat bercerita!');
    }

    public function show(CounselingSession $session, HexaAIService $hexaAI)
    {
        if ($session->user_id !== auth()->id() && auth()->user()->role !== 'doctor') {
            abort(403, 'Kamu tidak memiliki akses ke sesi ini.');
        }

        $messages = $session->chatMessages()->oldest()->get();

        $reflectionKeywords = [];
        $actionSteps = [];

        if ($session->status === 'finished' && auth()->user()->role !== 'doctor') {
            $reflectionKeywords = $hexaAI->summarizeKeywords($session);
            $actionSteps = $hexaAI->generateActionSteps($session);
        }

        $lastAiMessageRaw = $session->chatMessages()
            ->where('sender', 'ai')
            ->latest()
            ->first();

        $lastAiMessage = $lastAiMessageRaw ? \Illuminate\Support\Str::limit(strip_tags($lastAiMessageRaw->message), 300) : '';

        return view('sessions.show', compact('session', 'messages', 'reflectionKeywords', 'actionSteps', 'lastAiMessage'));
    }

    public function doctorShow(CounselingSession $session, HexaAIService $hexaAI)
    {
        if (auth()->user()->role !== 'doctor') {
            abort(403, 'Hanya dokter yang dapat mengakses halaman ini.');
        }

        $messages = $session->chatMessages()->oldest()->get();

        $reflectionKeywords = [];
        $actionSteps = [];

        if ($session->status === 'finished') {
            $reflectionKeywords = $hexaAI->summarizeKeywords($session);
            $actionSteps = $hexaAI->generateActionSteps($session);
        }

        return view('sessions.show', compact('session', 'messages', 'reflectionKeywords', 'actionSteps'));
    }

    public function finish(Request $request, CounselingSession $session, HexaAIService $hexaAI)
    {
        if ($session->user_id !== auth()->id()) {
            abort(403, 'Kamu tidak memiliki akses ke sesi ini.');
        }

        $request->validate([
            'final_mood' => 'nullable|string|in:lebih_tenang,sama_saja,butuh_bantuan',
        ]);

        $finalMood = $request->final_mood;

        $session->update([
            'status' => 'finished',
            'final_mood' => $finalMood,
            'is_escalated' => $finalMood === 'butuh_bantuan',
        ]);

        $reflectionKeywords = $hexaAI->summarizeKeywords($session);
        $actionSteps = $hexaAI->generateActionSteps($session);

        return redirect()->route('sessions.show', $session)
            ->with('show_reflection', true)
            ->with('reflection_keywords', $reflectionKeywords)
            ->with('action_steps', $actionSteps);
    }

    public function updateNotes(Request $request, CounselingSession $session)
    {
        if (auth()->user()->role !== 'doctor') {
            abort(403, 'Hanya dokter yang dapat mengakses halaman ini.');
        }

        $request->validate([
            'doctor_notes' => 'nullable|string|max:5000',
        ]);

        $session->update([
            'doctor_notes' => $request->doctor_notes,
        ]);

        return back()->with('success', 'Catatan klinis berhasil disimpan.');
    }
}
