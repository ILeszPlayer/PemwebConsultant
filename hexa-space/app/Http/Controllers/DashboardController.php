<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CounselingSession;
use App\Models\DailyJournal;
use Carbon\Carbon;

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

        // Journal streak
        $streak = 0;
        $checkDate = Carbon::today();
        while (DailyJournal::where('user_id', $user->id)->whereDate('created_at', $checkDate)->exists()) {
            $streak++;
            $checkDate->subDay();
        }

        // Daily motivational quote
        $quotes = [
            "Kamu tidak sendiri. Setiap langkah kecil yang kamu ambil hari ini adalah kemenangan.",
            "Berani cerita adalah langkah pertama menuju pemulihan. Kamu hebat!",
            "Kesehatan mental itu sama pentingnya dengan kesehatan fisik. Jaga dirimu.",
            "Kamu lebih kuat dari yang kamu kira. Buktinya kamu masih bertahan sampai hari ini.",
            "Tidak apa-apa untuk tidak baik-baik saja. Yang penting kamu terus berusaha.",
            "Setiap awan gelap pasti berlalu. Begitu juga dengan masalahmu.",
            "Kamu berharga dan berhak untuk bahagia. Ingat itu.",
            "Proses penyembuhan itu tidak linear, dan itu tidak apa-apa.",
        ];
        $dailyQuote = $quotes[now()->dayOfYear % count($quotes)];

        $moodScore = ['😭' => 1, '🙁' => 2, '😐' => 3, '🙂' => 4, '😊' => 5];
        $weeklyMoodScore = 0;
        $weeklyMoodCount = 0;
        foreach ($recentJournals as $j) {
            if (isset($moodScore[$j->mood_emoji])) {
                $weeklyMoodScore += $moodScore[$j->mood_emoji];
                $weeklyMoodCount++;
            }
        }
        $avgMoodScore = $weeklyMoodCount > 0 ? round($weeklyMoodScore / $weeklyMoodCount, 1) : null;

        return view('dashboard', compact(
            'sessions', 'recentJournals', 'moodTotals', 'grandTotal',
            'totalSessions', 'activeSessions', 'finishedSessions',
            'totalJournals', 'totalChatMessages', 'streak', 'dailyQuote', 'avgMoodScore'
        ));
    }
}
