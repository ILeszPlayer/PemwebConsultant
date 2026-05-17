<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CounselingSession;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->role === 'doctor') {
            $totalActive = CounselingSession::where('status', 'active')->count();
            $totalFinished = CounselingSession::where('status', 'finished')->count();
            $sessions = CounselingSession::with('user', 'counselingService')
                ->latest()
                ->get();

            return view('dashboard', compact('totalActive', 'totalFinished', 'sessions'));
        }

        $sessions = CounselingSession::where('user_id', $user->id)
            ->latest()
            ->take(3)
            ->get();

        return view('dashboard', compact('sessions'));
    }
}
