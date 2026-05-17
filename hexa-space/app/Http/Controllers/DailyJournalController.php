<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DailyJournal;

class DailyJournalController extends Controller
{
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
}
