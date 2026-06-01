<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CounselingSession;
use App\Models\CounselingService;
use Illuminate\Support\Facades\Auth;

class CounselingSessionController extends Controller
{
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

    public function show(CounselingSession $session)
    {
        if ($session->user_id !== auth()->id() && auth()->user()->role !== 'doctor') {
            abort(403, 'Kamu tidak memiliki akses ke sesi ini.');
        }

        $messages = $session->chatMessages()->oldest()->get();

        return view('sessions.show', compact('session', 'messages'));
    }

    public function doctorShow(CounselingSession $session)
    {
        if (auth()->user()->role !== 'doctor') {
            abort(403, 'Hanya dokter yang dapat mengakses halaman ini.');
        }

        $messages = $session->chatMessages()->oldest()->get();

        return view('sessions.show', compact('session', 'messages'));
    }

    public function index(Request $request)
    {
        $query = CounselingSession::where('user_id', auth()->id());

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhereHas('counselingService', fn($sq) => $sq->where('name', 'like', "%{$search}%"));
            });
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        $sessions = $query->latest()->get();

        return view('sessions.index', compact('sessions'));
    }

    public function updateTitle(Request $request, CounselingSession $session)
    {
        if ($session->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $session->update([
            'title' => $request->title,
        ]);

        return back()->with('success', 'Judul sesi berhasil diperbarui.');
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
