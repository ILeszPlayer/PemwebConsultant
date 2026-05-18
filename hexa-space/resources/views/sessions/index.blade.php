<x-app-layout>
    <div class="py-12 bg-[#F8F3FF] min-h-screen">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-[#374151]">Riwayat Sesi Konseling</h1>
                <p class="text-[#6B7280] mt-2">Lihat kembali sesi konseling yang pernah kamu lakukan.</p>
            </div>

            @if(session('success'))
                <div class="bg-[#FCE7F3] border border-[#FBCFE8] text-[#BE185D] rounded-2xl px-6 py-4 mb-6">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-700 rounded-2xl px-6 py-4 mb-6">
                    {{ session('error') }}
                </div>
            @endif

            @if($sessions->count() > 0)
                <div class="space-y-4">
                    @foreach($sessions as $session)
                        <a href="{{ route('sessions.show', $session) }}" class="bg-white rounded-2xl p-6 shadow-sm border border-[#E5E7EB] hover:shadow-md transition block">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-2 flex-wrap">
                                        <span class="font-semibold text-lg text-[#374151]">{{ $session->title }}</span>
                                        @if($session->status === 'active')
                                            <span class="px-3 py-1 rounded-full text-xs font-medium bg-[#FBCFE8] text-[#BE185D]">Active</span>
                                        @else
                                            <span class="px-3 py-1 rounded-full text-xs font-medium bg-[#E5E7EB] text-[#6B7280]">Selesai</span>
                                        @endif
                                        @if($session->is_escalated)
                                            <span class="px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-600">Butuh Bantuan</span>
                                        @endif
                                        @if($session->final_mood === 'lebih_tenang')
                                            <span class="px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-600">😊 Tenang</span>
                                        @elseif($session->final_mood === 'sama_saja')
                                            <span class="px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-600">😐 Sama</span>
                                        @elseif($session->final_mood === 'butuh_bantuan')
                                            <span class="px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-600">🙁 Butuh Bantuan</span>
                                        @endif
                                    </div>
                                    <p class="text-sm text-[#6B7280]">{{ $session->counselingService->name ?? 'Layanan' }}</p>
                                </div>
                                <div class="text-right text-xs text-[#9CA3AF] shrink-0 ml-4">
                                    <p>{{ $session->created_at->format('d M Y') }}</p>
                                    <p>{{ $session->created_at->format('H:i') }}</p>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="bg-white rounded-2xl p-12 shadow-sm border border-[#E5E7EB] text-center">
                    <div class="text-5xl mb-4">📋</div>
                    <p class="text-[#6B7280] text-lg mb-2">Belum ada sesi konseling.</p>
                    <p class="text-[#9CA3AF] text-sm mb-6">Yuk mulai cerita pertamamu bersama Hexa Space.</p>
                    <a href="{{ route('services.index') }}" class="inline-block bg-[#C084FC] hover:bg-[#7E22CE] text-white px-6 py-3 rounded-full font-medium transition shadow-sm hover:shadow-md">Mulai Konseling</a>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
