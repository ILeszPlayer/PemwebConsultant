<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DailyJournal;
use Carbon\Carbon;

class DailyJournalController extends Controller
{
    public function index()
    {
        $userId = auth()->id();

        $journals = DailyJournal::where('user_id', $userId)
            ->latest()
            ->get();

        $moodCounts = DailyJournal::where('user_id', $userId)
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

        $weeklyMoods = DailyJournal::where('user_id', $userId)
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

        // Journal streak
        $streak = 0;
        $checkDate = Carbon::today();
        while (DailyJournal::where('user_id', $userId)->whereDate('created_at', $checkDate)->exists()) {
            $streak++;
            $checkDate->subDay();
        }

        // Calendar heatmap — last 90 days
        $heatmapData = DailyJournal::where('user_id', $userId)
            ->where('created_at', '>=', now()->subDays(90))
            ->selectRaw('DATE(created_at) as date, mood_emoji')
            ->get()
            ->groupBy('date');

        $moodColors = ['😭' => '#fca5a5', '🙁' => '#fdba74', '😐' => '#fde68a', '🙂' => '#86efac', '😊' => '#c084fc'];

        $heatmap = [];
        $start = Carbon::today()->subDays(89);
        for ($i = 0; $i < 90; $i++) {
            $d = $start->copy()->addDays($i);
            $key = $d->format('Y-m-d');
            if (isset($heatmapData[$key])) {
                $emoji = $heatmapData[$key]->first()->mood_emoji;
                $heatmap[] = ['date' => $key, 'mood' => $emoji, 'color' => $moodColors[$emoji] ?? '#e5e7eb', 'score' => $moodScore[$emoji] ?? 3];
            } else {
                $heatmap[] = ['date' => $key, 'mood' => null, 'color' => '#f3f4f6', 'score' => 0];
            }
        }

        return view('journal.index', compact(
            'journals', 'moodTotals', 'grandTotal', 'weeklyChart',
            'streak', 'heatmap'
        ));
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
