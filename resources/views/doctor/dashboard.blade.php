<x-app-layout>
    <div class="py-12 bg-[#F8F3FF] min-h-screen">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-[#374151]">Halo, Dr. {{ Auth::user()->name }}!</h1>
                <p class="text-[#6B7280] mt-2">Panel monitoring klinis pasien Hexa Space.</p>
            </div>

            <div class="grid md:grid-cols-3 gap-6 mb-8">
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
                        <div class="bg-green-100 rounded-2xl w-14 h-14 flex items-center justify-center text-2xl">✅</div>
                        <div>
                            <p class="text-sm text-[#6B7280]">Sesi Selesai</p>
                            <p class="text-3xl font-bold text-[#374151]">{{ $totalFinished ?? 0 }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-[#E5E7EB]">
                    <div class="flex items-center gap-4">
                        <div class="bg-rose-100 rounded-2xl w-14 h-14 flex items-center justify-center text-2xl">🚨</div>
                        <div>
                            <p class="text-sm text-[#6B7280]">Eskalasi Butuh Bantuan</p>
                            <p class="text-3xl font-bold text-rose-600">{{ $escalatedSessions->count() ?? 0 }}</p>
                        </div>
                    </div>
                </div>
            </div>

            @if(isset($escalatedSessions) && $escalatedSessions->count() > 0)
                <div class="bg-rose-50 border border-rose-200 rounded-2xl p-6 mb-8">
                    <h2 class="text-lg font-bold text-rose-700 mb-4 flex items-center gap-2">
                        <span>🚨</span> Prioritas — Pasien Butuh Bantuan Ahli
                    </h2>
                    <div class="space-y-4">
                        @foreach($escalatedSessions as $session)
                            <div class="bg-white rounded-2xl p-5 shadow-sm border border-rose-100">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="font-semibold text-[#374151]">{{ $session->user->name }}</p>
                                        <p class="text-sm text-[#6B7280]">{{ $session->counselingService->name ?? 'Konseling' }}</p>
                                        <p class="text-xs text-gray-400 mt-1">{{ $session->created_at->diffForHumans() }}</p>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-rose-100 text-rose-600">Eskalasi</span>
                                        <a href="{{ route('doctor.sessions.show', $session) }}" class="px-4 py-2 bg-rose-500 hover:bg-rose-600 text-white text-xs rounded-full font-medium transition shadow-sm">Lihat Detail</a>
                                    </div>
                                </div>
                                @if($session->final_mood)
                                    <div class="mt-2 text-xs text-[#6B7280]">
                                        Mood Akhir: 
                                        @if($session->final_mood === 'better') 😊 Lebih Tenang
                                        @elseif($session->final_mood === 'need_doctor') 🙁 Butuh Bantuan Ahli
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="bg-white rounded-2xl p-6 shadow-sm border border-[#E5E7EB]">
                <h2 class="text-lg font-bold text-[#374151] mb-4">Semua Sesi Konseling</h2>
                @if(isset($sessions) && $sessions->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-[#E5E7EB]">
                                    <th class="text-left py-3 px-4 text-[#6B7280] font-medium">Pasien</th>
                                    <th class="text-left py-3 px-4 text-[#6B7280] font-medium">Layanan</th>
                                    <th class="text-left py-3 px-4 text-[#6B7280] font-medium">Status</th>
                                    <th class="text-left py-3 px-4 text-[#6B7280] font-medium">Tanggal</th>
                                    <th class="text-left py-3 px-4 text-[#6B7280] font-medium">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sessions as $session)
                                    <tr class="border-b border-[#F3F4F6] hover:bg-[#F8F3FF]/50 transition">
                                        <td class="py-3 px-4 font-medium text-[#374151]">{{ $session->user->name }}</td>
                                        <td class="py-3 px-4 text-[#6B7280]">{{ $session->counselingService->name ?? '-' }}</td>
                                        <td class="py-3 px-4">
                                            @if($session->status === 'active')
                                                <span class="px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-700">Aktif</span>
                                            @else
                                                <span class="px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-500">Selesai</span>
                                            @endif
                                            @if($session->is_escalated)
                                                <span class="px-3 py-1 rounded-full text-xs font-medium bg-rose-100 text-rose-600 ml-1">Eskalasi</span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-4 text-[#6B7280]">{{ $session->created_at->format('d M Y') }}</td>
                                        <td class="py-3 px-4">
                                            <a href="{{ route('doctor.sessions.show', $session) }}" class="text-[#C084FC] hover:text-[#7E22CE] font-medium text-xs transition">Lihat Detail</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-center text-[#9CA3AF] py-8">Belum ada sesi konseling.</p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
