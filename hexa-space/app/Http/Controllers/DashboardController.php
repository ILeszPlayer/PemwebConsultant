<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CounselingSession;
use App\Models\DailyJournal;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        if ($user->role === 'doctor') {
            $totalActive = CounselingSession::where('status', 'active')->count();
            $totalFinished = CounselingSession::where('status', 'finished')->count();
            $escalatedSessions = CounselingSession::with('user', 'counselingService')
                ->where('is_escalated', true)
                ->latest()
                ->get();
            $sessions = CounselingSession::with('user', 'counselingService')
                ->latest()
                ->get();

            return view('doctor.dashboard', compact('totalActive', 'totalFinished', 'escalatedSessions', 'sessions'));
        }

        $totalSessions = CounselingSession::where('user_id', $user->id)->count();
        $activeSessions = CounselingSession::where('user_id', $user->id)->where('status', 'active')->count();
        $finishedSessions = CounselingSession::where('user_id', $user->id)->where('status', 'finished')->count();
        $totalJournals = DailyJournal::where('user_id', $user->id)->count();
        $totalChatMessages = \App\Models\ChatMessage::whereIn('counseling_session_id', function($q) use ($user) {
            $q->select('id')->from('counseling_sessions')->where('user_id', $user->id);
        })->count();

        $sessions = CounselingSession::where('user_id', $user->id)
            ->latest()
            ->take(3)
            ->get();

        $recentJournals = DailyJournal::where('user_id', $user->id)
            ->latest()
            ->take(7)
            ->get();

        $moodCounts = DailyJournal::where('user_id', $user->id)
            ->where('created_at', '>=', now()->subDays(7))
            ->selectRaw('mood_emoji, COUNT(*) as count')
            ->groupBy('mood_emoji')
            ->pluck('count', 'mood_emoji');

        $moodEmojis = ['😭', '🙁', '😐', '🙂', '😊'];
        $moodTotals = [];
        $grandTotal = 0;
        foreach ($moodEmojis as $emoji) {
            $cnt = $moodCounts[$emoji] ?? 0;
            $moodTotals[$emoji] = $cnt;
            $grandTotal += $cnt;
        }

        return view('dashboard', compact('sessions', 'recentJournals', 'moodTotals', 'grandTotal', 'totalSessions', 'activeSessions', 'finishedSessions', 'totalJournals', 'totalChatMessages'));
    }
}
