<x-app-layout>
    <div class="py-12 bg-[#F8F3FF] min-h-screen">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-[#374151]">Riwayat Jurnal Perasaan</h1>
                <p class="text-[#6B7280] mt-2">Semua catatan perasaan yang pernah kamu tulis.</p>
            </div>

            {{-- Mood Stats Summary --}}
            @if($grandTotal > 0)
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-[#E5E7EB] mb-8">
                    <h2 class="text-lg font-semibold text-[#374151] mb-4">📊 Statistik Mood (30 Hari Terakhir)</h2>
                    <div class="space-y-3">
                        @php $moodLabels = ['😭' => 'Sangat Buruk', '🙁' => 'Buruk', '😐' => 'Biasa', '🙂' => 'Baik', '😊' => 'Sangat Baik']; @endphp
                        @foreach(['😭', '🙁', '😐', '🙂', '😊'] as $emoji)
                            @php
                                $count = $moodTotals[$emoji] ?? 0;
                                $percentage = $grandTotal > 0 ? max(round(($count / $grandTotal) * 100), 2) : 0;
                                $barColors = ['😭' => 'bg-red-400', '🙁' => 'bg-orange-400', '😐' => 'bg-yellow-400', '🙂' => 'bg-green-400', '😊' => 'bg-purple-400'];
                            @endphp
                            <div class="flex items-center gap-3">
                                <span class="text-lg w-8 text-center">{{ $emoji }}</span>
                                <span class="text-xs text-[#6B7280] w-20">{{ $moodLabels[$emoji] }}</span>
                                <div class="flex-1 bg-gray-100 rounded-full h-6 overflow-hidden">
                                    <div class="{{ $barColors[$emoji] }} h-full rounded-full transition-all duration-700 flex items-center justify-end pr-2" style="width: {{ $percentage }}%; min-width: {{ $count > 0 ? '2rem' : '0' }}">
                                        @if($count > 0)
                                            <span class="text-[10px] text-white font-bold">{{ $count }}</span>
                                        @endif
                                    </div>
                                </div>
                                <span class="text-xs font-medium text-[#6B7280] w-12 text-right">{{ $percentage }}%</span>
                            </div>
                        @endforeach
                    </div>
                    <p class="text-xs text-[#9CA3AF] mt-4 text-center">Total {{ $grandTotal }} catatan mood dalam 30 hari terakhir</p>
                </div>
            @endif

            {{-- Weekly Mood Trend --}}
            @if(isset($weeklyChart) && count($weeklyChart) > 0)
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-[#E5E7EB] mb-8">
                    <h2 class="text-lg font-semibold text-[#374151] mb-4">📈 Tren Mood Minggu Ini</h2>
                    <div class="flex items-end gap-3 h-40">
                        @php
                            $maxScore = 5;
                            $moodScoreLabels = [1 => '😭', 2 => '🙁', 3 => '😐', 4 => '🙂', 5 => '😊'];
                            $barColors = ['😭' => 'bg-red-400', '🙁' => 'bg-orange-400', '😐' => 'bg-yellow-400', '🙂' => 'bg-green-400', '😊' => 'bg-purple-400'];
                        @endphp
                        @foreach($weeklyChart as $date => $score)
                            @php
                                $height = max(($score / $maxScore) * 100, 10);
                                $dateObj = \Carbon\Carbon::parse($date);
                                $dayName = $dateObj->format('D');
                                $moodEmoji = $moodScoreLabels[round($score)] ?? '😐';
                            @endphp
                            <div class="flex-1 flex flex-col items-center gap-2">
                                <span class="text-lg">{{ $moodEmoji }}</span>
                                <div class="w-full bg-gray-100 rounded-lg overflow-hidden flex-1 self-end" style="height: 160px;">
                                    <div class="w-full {{ $barColors[$moodEmoji] ?? 'bg-purple-400' }} rounded-lg transition-all duration-500" style="height: {{ $height }}%; margin-top: auto;"></div>
                                </div>
                                <span class="text-[10px] text-[#6B7280] font-medium">{{ $dayName }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Journal Entries --}}
            @forelse($journals as $journal)
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-[#E5E7EB] mb-4 hover:shadow-md transition">
                    <div class="flex items-start gap-4">
                        <span class="text-4xl">{{ $journal->mood_emoji }}</span>
                        <div class="flex-1">
                            <p class="text-[#374151]">{{ $journal->note ?? '—' }}</p>
                            <p class="text-xs text-[#9CA3AF] mt-2">{{ $journal->created_at->format('d M Y H:i') }}</p>
                        </div>
                        <form action="{{ route('journal.destroy', $journal) }}" method="POST" onsubmit="return confirm('Hapus jurnal ini?')">
                            @csrf @method('DELETE')
                            <button class="text-red-400 hover:text-red-600 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-2xl p-16 shadow-sm border border-[#E5E7EB] text-center">
                    <div class="text-6xl mb-4">📖</div>
                    <h3 class="text-xl font-semibold text-[#374151] mb-2">Belum Ada Jurnal</h3>
                    <p class="text-[#6B7280]">Kamu belum pernah menulis jurnal perasaan.</p>
                    <a href="{{ route('dashboard') }}" class="inline-block mt-6 bg-[#C084FC] hover:bg-[#7E22CE] text-white px-6 py-3 rounded-full font-medium transition shadow-sm">Tulis Jurnal Sekarang</a>
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
