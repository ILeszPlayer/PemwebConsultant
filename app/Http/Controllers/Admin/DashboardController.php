<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\CounselingSession;
use App\Models\DailyJournal;
use App\Models\Article;

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

        return view('admin.dashboard', compact(
            'totalUsers', 'totalDoctors', 'totalAdmins',
            'activeSessions', 'totalSessions', 'escalatedSessions',
            'totalJournals', 'totalArticles', 'bannedUsers'
        ));
    }
}
