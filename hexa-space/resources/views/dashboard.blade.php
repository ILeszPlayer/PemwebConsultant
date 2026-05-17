<x-app-layout>
    <div class="py-12 bg-[#F8F3FF] min-h-screen">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            @if(auth()->user()->role === 'doctor')
                {{-- DOCTOR DASHBOARD --}}
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-[#374151]">Halo, Dr. {{ Auth::user()->name }}! 🩺</h1>
                    <p class="text-[#6B7280] mt-2">Selamat datang di panel monitoring Hexa Space.</p>
                </div>

                <div class="grid md:grid-cols-2 gap-6 mb-8">
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-[#E5E7EB]">
                        <div class="flex items-center gap-4">
                            <div class="bg-purple-100 rounded-2xl w-14 h-14 flex items-center justify-center text-2xl">💬</div>
                            <div>
                                <p class="text-sm text-[#6B7280]">Sesi Aktif</p>
                                <p class="text-3xl font-bold text-[#374151]">{{ $totalActive ?? 0 }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-[#E5E7EB]">
                        <div class="flex items-center gap-4">
                            <div class="bg-pink-100 rounded-2xl w-14 h-14 flex items-center justify-center text-2xl">✅</div>
                            <div>
                                <p class="text-sm text-[#6B7280]">Sesi Selesai</p>
                                <p class="text-3xl font-bold text-[#374151]">{{ $totalFinished ?? 0 }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                @if(isset($escalatedSessions) && $escalatedSessions->count() > 0)
                    <div class="bg-red-50 border border-red-200 rounded-2xl p-6 mb-8">
                        <h2 class="text-lg font-bold text-red-700 mb-4">🚨 Antrean Pasien Butuh Bantuan (Prioritas Eskalasi)</h2>
                        <div class="space-y-3">
                            @foreach($escalatedSessions as $session)
                                <div class="bg-white rounded-2xl p-4 shadow-sm border border-red-200">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="font-semibold text-[#374151]">{{ $session->title }}</p>
                                            <p class="text-sm text-[#6B7280]">Pasien: {{ $session->user->name }} &middot; {{ $session->counselingService->name ?? 'Layanan' }}</p>
                                        </div>
                                        <a href="{{ route('doctor.sessions.show', $session) }}" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-full text-sm font-medium transition shrink-0 ml-4">Lihat Detail Sesi</a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <h2 class="text-xl font-semibold text-[#374151] mb-4">Seluruh Riwayat Sesi Pantauan</h2>

                @if($sessions->count() > 0)
                    <div class="space-y-4">
                        @foreach($sessions as $session)
                            <div class="bg-white rounded-2xl p-6 shadow-sm border border-[#E5E7EB]">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-3 mb-2">
                                            <span class="font-semibold text-lg text-[#374151]">{{ $session->title }}</span>
                                            @if($session->status === 'active')
                                                <span class="px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-700">Active</span>
                                            @else
                                                <span class="px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-500">Selesai</span>
                                            @endif
                                            @if($session->is_escalated)
                                                <span class="px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-600">Butuh Bantuan</span>
                                            @endif
                                        </div>
                                        <p class="text-sm text-[#6B7280]">
                                            Pasien: <span class="font-medium">{{ $session->user->name }}</span>
                                            &middot; {{ $session->counselingService->name ?? 'Layanan' }}
                                        </p>
                                    </div>
                                    <div class="text-right shrink-0 ml-4">
                                        <p class="text-xs text-[#9CA3AF]">{{ $session->created_at->format('d M Y') }}</p>
                                        <a href="{{ route('doctor.sessions.show', $session) }}" class="inline-block mt-2 text-sm bg-[#C084FC] hover:bg-[#7E22CE] text-white px-4 py-2 rounded-full font-medium transition">Lihat Detail Sesi</a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="bg-white rounded-2xl p-12 shadow-sm border border-[#E5E7EB] text-center">
                        <div class="text-5xl mb-4">📋</div>
                        <p class="text-[#6B7280] text-lg">Belum ada sesi konseling.</p>
                    </div>
                @endif
            @else
                {{-- PATIENT DASHBOARD --}}
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-[#374151]">Halo, {{ Auth::user()->name }}! 👋</h1>
                    <p class="text-[#6B7280] mt-2">Selamat datang kembali di Hexa Space. Yuk, tulis jurnal harianmu atau lanjutkan sesi konseling.</p>
                </div>

                {{-- Daily Journal --}}
                <div class="bg-white rounded-2xl shadow-sm border border-[#E5E7EB] p-6 mb-8">
                    <h2 class="text-lg font-semibold text-[#374151] mb-4">📖 Jurnal Perasaan Hari Ini</h2>
                    <form action="{{ route('journal.store') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <p class="text-sm font-medium text-[#6B7280] mb-2">Bagaimana perasaanmu hari ini?</p>
                            <div class="flex gap-3" id="moodSelector">
                                <button type="button" onclick="selectMood('😭', this)" class="mood-btn text-3xl p-3 rounded-2xl border border-[#E5E7EB] hover:border-purple-300 hover:bg-purple-50 transition opacity-50 hover:opacity-100">😭</button>
                                <button type="button" onclick="selectMood('🙁', this)" class="mood-btn text-3xl p-3 rounded-2xl border border-[#E5E7EB] hover:border-purple-300 hover:bg-purple-50 transition opacity-50 hover:opacity-100">🙁</button>
                                <button type="button" onclick="selectMood('😐', this)" class="mood-btn text-3xl p-3 rounded-2xl border border-[#E5E7EB] hover:border-purple-300 hover:bg-purple-50 transition opacity-50 hover:opacity-100">😐</button>
                                <button type="button" onclick="selectMood('🙂', this)" class="mood-btn text-3xl p-3 rounded-2xl border border-[#E5E7EB] hover:border-purple-300 hover:bg-purple-50 transition opacity-50 hover:opacity-100">🙂</button>
                                <button type="button" onclick="selectMood('😊', this)" class="mood-btn text-3xl p-3 rounded-2xl border border-[#E5E7EB] hover:border-purple-300 hover:bg-purple-50 transition opacity-50 hover:opacity-100">😊</button>
                            </div>
                            <input type="hidden" name="mood_emoji" id="selectedMood" value="">
                        </div>
                        <div class="mb-4">
                            <textarea name="note" rows="2" class="w-full border border-[#E5E7EB] rounded-2xl px-5 py-3 text-[#374151] placeholder-[#9CA3AF] focus:outline-none focus:ring-2 focus:ring-[#C084FC] focus:border-transparent resize-none" placeholder="Tulis satu kalimat tentang harimu…" maxlength="500"></textarea>
                        </div>
                        <div class="text-right">
                            <button type="submit" id="journalSubmit" class="bg-[#C084FC] hover:bg-[#7E22CE] text-white px-6 py-2 rounded-full text-sm font-medium transition" disabled>Simpan Jurnal</button>
                        </div>
                    </form>
                </div>

                {{-- Mood Graph --}}
                @if($grandTotal > 0)
                    <div class="bg-white rounded-2xl shadow-sm border border-[#E5E7EB] p-6 mb-8">
                        <h2 class="text-lg font-semibold text-[#374151] mb-4">📊 Grafik Mood Minggu Ini</h2>
                        <div class="space-y-3">
                            @php $moodLabels = ['😭' => 'Sangat Buruk', '🙁' => 'Buruk', '😐' => 'Biasa', '🙂' => 'Baik', '😊' => 'Sangat Baik']; @endphp
                            @foreach(['😭', '🙁', '😐', '🙂', '😊'] as $emoji)
                                @php
                                    $count = $moodTotals[$emoji] ?? 0;
                                    $percentage = $grandTotal > 0 ? round(($count / $grandTotal) * 100) : 0;
                                    $barColors = ['😭' => 'bg-red-400', '🙁' => 'bg-orange-400', '😐' => 'bg-yellow-400', '🙂' => 'bg-green-400', '😊' => 'bg-purple-400'];
                                @endphp
                                <div class="flex items-center gap-3">
                                    <span class="text-lg w-8 text-center">{{ $emoji }}</span>
                                    <span class="text-xs text-[#6B7280] w-20">{{ $moodLabels[$emoji] }}</span>
                                    <div class="flex-1 bg-gray-100 rounded-full h-5 overflow-hidden">
                                        <div class="{{ $barColors[$emoji] }} h-full rounded-full transition-all duration-500" style="width: {{ $percentage }}%"></div>
                                    </div>
                                    <span class="text-xs font-medium text-[#6B7280] w-10 text-right">{{ $percentage }}%</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Recent Journals --}}
                @if($recentJournals->count() > 0)
                    <div class="bg-white rounded-2xl shadow-sm border border-[#E5E7EB] p-6 mb-8">
                        <h2 class="text-lg font-semibold text-[#374151] mb-4">📝 Riwayat Jurnal Terakhir</h2>
                        <div class="space-y-3">
                            @foreach($recentJournals as $journal)
                                <div class="flex items-start gap-3 p-3 bg-[#F8F3FF] rounded-2xl">
                                    <span class="text-2xl">{{ $journal->mood_emoji }}</span>
                                    <div class="flex-1">
                                        <p class="text-sm text-[#374151]">{{ $journal->note ?? '—' }}</p>
                                        <p class="text-xs text-[#9CA3AF] mt-1">{{ $journal->created_at->format('d M Y H:i') }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- CTA & Sessions --}}
                <div class="bg-white rounded-2xl shadow-sm border border-[#E5E7EB] p-8 mb-8">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div>
                            <h2 class="text-xl font-semibold text-[#374151]">Mulai Sesi Konseling Baru</h2>
                            <p class="text-[#6B7280] mt-1">Pilih layanan konseling yang sesuai dengan kebutuhanmu.</p>
                        </div>
                        <a href="{{ route('services.index') }}" class="inline-block bg-[#C084FC] hover:bg-[#7E22CE] text-white px-6 py-3 rounded-full font-medium transition text-center shadow-sm hover:shadow-md">Pilih Layanan</a>
                    </div>
                </div>

                <h2 class="text-xl font-semibold text-[#374151] mb-4">Sesi Terakhir</h2>

                @if($sessions->count() > 0)
                    <div class="grid md:grid-cols-3 gap-6">
                        @foreach($sessions as $session)
                            <a href="{{ route('sessions.show', $session) }}" class="bg-white rounded-2xl p-6 shadow-sm border border-[#E5E7EB] hover:shadow-md transition block">
                                <div class="flex items-center justify-between mb-3">
                                    <span class="font-semibold text-[#374151]">{{ $session->title }}</span>
                                    @if($session->status === 'active')
                                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-700">Active</span>
                                    @else
                                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-500">Selesai</span>
                                    @endif
                                </div>
                                <p class="text-sm text-[#6B7280]">{{ $session->counselingService->name ?? 'Layanan' }}</p>
                                <p class="text-xs text-[#9CA3AF] mt-2">{{ $session->created_at->diffForHumans() }}</p>
                            </a>
                        @endforeach
                    </div>
                    <div class="mt-6 text-center">
                        <a href="{{ route('sessions.index') }}" class="text-[#C084FC] hover:text-[#7E22CE] font-medium text-sm transition">Lihat Semua Riwayat Sesi &rarr;</a>
                    </div>
                @else
                    <div class="bg-white rounded-2xl p-12 shadow-sm border border-[#E5E7EB] text-center">
                        <div class="text-5xl mb-4">💬</div>
                        <p class="text-[#6B7280] text-lg mb-2">Belum ada sesi konseling.</p>
                        <p class="text-[#9CA3AF] text-sm mb-6">Yuk mulai cerita pertamamu bersama Hexa Space.</p>
                        <a href="{{ route('services.index') }}" class="inline-block bg-[#C084FC] hover:bg-[#7E22CE] text-white px-6 py-3 rounded-full font-medium transition shadow-sm hover:shadow-md">Mulai Konseling</a>
                    </div>
                @endif
            @endif
        </div>
    </div>

    <script>
        let selectedMoodValue = '';

        function selectMood(emoji, btn) {
            selectedMoodValue = emoji;
            document.getElementById('selectedMood').value = emoji;
            document.querySelectorAll('.mood-btn').forEach(el => {
                el.classList.remove('opacity-100', 'border-purple-400', 'bg-purple-50');
                el.classList.add('opacity-50');
            });
            btn.classList.remove('opacity-50');
            btn.classList.add('opacity-100', 'border-purple-400', 'bg-purple-50');
            document.getElementById('journalSubmit').disabled = false;
        }
    </script>
</x-app-layout>
