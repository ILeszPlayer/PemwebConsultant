<x-app-layout>
    <div class="py-12 bg-[#F8F3FF] min-h-screen">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-8">
                <h1 class="text-3xl font-bold text-[#374151]">Riwayat Sesi Konseling</h1>
                <a href="{{ route('services.index') }}" class="bg-[#C084FC] hover:bg-[#7E22CE] text-white px-6 py-3 rounded-full font-medium transition shadow-sm">+ Sesi Baru</a>
            </div>

            {{-- Search & Filter --}}
            <div class="bg-white rounded-2xl p-4 shadow-sm border border-[#E5E7EB] mb-6">
                <form method="GET" class="flex flex-wrap gap-4 items-end">
                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-sm font-medium text-[#6B7280] mb-1">Cari Sesi</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul atau layanan..." class="w-full border border-[#E5E7EB] rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#C084FC]">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-[#6B7280] mb-1">Status</label>
                        <select name="status" class="border border-[#E5E7EB] rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#C084FC]">
                            <option value="">Semua</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                            <option value="finished" {{ request('status') === 'finished' ? 'selected' : '' }}>Selesai</option>
                        </select>
                    </div>
                    <button type="submit" class="bg-[#C084FC] hover:bg-[#7E22CE] text-white px-5 py-2 rounded-xl text-sm font-medium transition">Filter</button>
                    @if(request('search') || request('status'))
                        <a href="{{ route('sessions.index') }}" class="text-[#6B7280] hover:text-[#374151] text-sm font-medium px-4 py-2">Reset</a>
                    @endif
                </form>
            </div>

            @forelse($sessions as $session)
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-[#E5E7EB] mb-4 hover:shadow-md transition">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <h3 class="font-semibold text-lg text-[#374151]">{{ $session->title }}</h3>
                                @if($session->status === 'active')
                                    <span class="px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-700">Aktif</span>
                                @else
                                    <span class="px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-500">Selesai</span>
                                @endif
                                @if($session->is_escalated)
                                    <span class="px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-600">Butuh Bantuan</span>
                                @endif
                            </div>
                            <p class="text-sm text-[#6B7280]">
                                {{ $session->counselingService->name ?? 'Layanan' }}
                                &middot; {{ $session->created_at->format('d M Y H:i') }}
                                @if($session->final_mood)
                                    &middot; Mood akhir: 
                                    @if($session->final_mood === 'better') 😊 Lebih Tenang
                                    @elseif($session->final_mood === 'need_doctor') 🙁 Butuh Dokter
                                    @endif
                                @endif
                            </p>
                        </div>
                        <div class="flex items-center gap-2 shrink-0 ml-4">
                            @if($session->status === 'active')
                                <a href="{{ route('sessions.show', $session) }}" class="bg-[#C084FC] hover:bg-[#7E22CE] text-white px-5 py-2 rounded-full text-sm font-medium transition">Lanjutkan</a>
                            @else
                                <a href="{{ route('sessions.show', $session) }}" class="bg-gray-100 hover:bg-gray-200 text-[#374151] px-5 py-2 rounded-full text-sm font-medium transition">Lihat</a>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-2xl p-16 shadow-sm border border-[#E5E7EB] text-center">
                    <div class="text-6xl mb-4">💬</div>
                    <h3 class="text-xl font-semibold text-[#374151] mb-2">Belum Ada Sesi Konseling</h3>
                    <p class="text-[#6B7280] mb-6">Mulai sesi konseling pertamamu dengan Hexa AI.</p>
                    <a href="{{ route('services.index') }}" class="inline-block bg-[#C084FC] hover:bg-[#7E22CE] text-white px-6 py-3 rounded-full font-medium transition">Mulai Konseling</a>
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
