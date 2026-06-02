<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\CounselingSession;
use App\Models\DailyJournal;
use App\Models\Article;
use App\Models\ChatMessage;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $totalUsers = User::where('role', 'user')->count();
        $totalDoctors = User::where('role', 'doctor')->count();
        $totalAdmins = User::where('role', 'admin')->count();

        $activeSessions = CounselingSession::where('status', 'active')->count();
        $totalSessions = CounselingSession::count();
        $escalatedSessions = CounselingSession::where('is_escalated', true)->count();
        $totalJournals = DailyJournal::count();
        $totalArticles = Article::count();

        $bannedUsers = User::where('is_banned', true)->count();

        // Session activity — last 7 days
        $sessionActivity = CounselingSession::where('created_at', '>=', now()->subDays(7))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date');

        $chartLabels = [];
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $d = Carbon::today()->subDays($i);
            $key = $d->format('Y-m-d');
            $chartLabels[] = $d->format('D');
            $chartData[] = $sessionActivity[$key] ?? 0;
        }

        // Recent activity feed
        $recentMessages = ChatMessage::with('counselingSession.user')
            ->latest()
            ->take(10)
            ->get();

        $recentJournals = DailyJournal::with('user')
            ->latest()
            ->take(5)
            ->get();

        $recentUsers = User::where('role', 'user')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalUsers', 'totalDoctors', 'totalAdmins',
            'activeSessions', 'totalSessions', 'escalatedSessions',
            'totalJournals', 'totalArticles', 'bannedUsers',
            'chartLabels', 'chartData', 'recentMessages',
            'recentJournals', 'recentUsers'
        ));
    }
}
