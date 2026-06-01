<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DailyJournal;

class DailyJournalController extends Controller
{
    public function index()
    {
        $journals = DailyJournal::where('user_id', auth()->id())
            ->latest()
            ->get();

        $moodCounts = DailyJournal::where('user_id', auth()->id())
            ->where('created_at', '>=', now()->subDays(30))
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

        $weeklyMoods = DailyJournal::where('user_id', auth()->id())
            ->where('created_at', '>=', now()->subDays(7))
            ->selectRaw('DATE(created_at) as date, mood_emoji')
            ->orderBy('date')
            ->get()
            ->groupBy('date');

        $moodScore = ['😭' => 1, '🙁' => 2, '😐' => 3, '🙂' => 4, '😊' => 5];
        $weeklyChart = [];
        foreach ($weeklyMoods as $date => $entries) {
            $avgScore = round($entries->avg(fn($e) => $moodScore[$e->mood_emoji] ?? 3), 1);
            $weeklyChart[$date] = $avgScore;
        }

        return view('journal.index', compact('journals', 'moodTotals', 'grandTotal', 'weeklyChart'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'mood_emoji' => 'required|string',
            'note' => 'nullable|string|max:500',
        ]);

        DailyJournal::create([
            'user_id' => auth()->id(),
            'mood_emoji' => $request->mood_emoji,
            'note' => $request->note,
        ]);

        return back()->with('success', 'Jurnal perasaan berhasil disimpan.');
    }

    public function destroy(DailyJournal $journal)
    {
        if ($journal->user_id !== auth()->id()) {
            abort(403);
        }

        $journal->delete();

        return back()->with('success', 'Jurnal berhasil dihapus.');
    }
}
