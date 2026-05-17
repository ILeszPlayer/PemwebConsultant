<x-app-layout>
    <div class="py-12 bg-[#F8F3FF] min-h-screen">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            @if(auth()->user()->role === 'doctor')
                {{-- DOCTOR DASHBOARD --}}
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-[#374151]">Halo, Dr. {{ Auth::user()->name }}! 🩺</h1>
                    <p class="text-[#6B7280] mt-2">Selamat datang di panel monitoring Hexa Space. Pantau sesi konseling pasien di sini.</p>
                </div>

                <div class="grid md:grid-cols-2 gap-6 mb-8">
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-[#E5E7EB]">
                        <div class="flex items-center gap-4">
                            <div class="bg-[#E9D5FF] rounded-2xl w-14 h-14 flex items-center justify-center text-2xl">💬</div>
                            <div>
                                <p class="text-sm text-[#6B7280]">Sesi Aktif</p>
                                <p class="text-3xl font-bold text-[#374151]">{{ $totalActive ?? 0 }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-[#E5E7EB]">
                        <div class="flex items-center gap-4">
                            <div class="bg-[#FCE7F3] rounded-2xl w-14 h-14 flex items-center justify-center text-2xl">✅</div>
                            <div>
                                <p class="text-sm text-[#6B7280]">Sesi Selesai</p>
                                <p class="text-3xl font-bold text-[#374151]">{{ $totalFinished ?? 0 }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <h2 class="text-xl font-semibold text-[#374151] mb-4">Semua Sesi Konseling</h2>

                @if($sessions->count() > 0)
                    <div class="space-y-4">
                        @foreach($sessions as $session)
                            <div class="bg-white rounded-2xl p-6 shadow-sm border border-[#E5E7EB]">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-3 mb-2">
                                            <span class="font-semibold text-lg text-[#374151]">{{ $session->title }}</span>
                                            @if($session->status === 'active')
                                                <span class="px-3 py-1 rounded-full text-xs font-medium bg-[#FBCFE8] text-[#BE185D]">Active</span>
                                            @else
                                                <span class="px-3 py-1 rounded-full text-xs font-medium bg-[#E5E7EB] text-[#6B7280]">Selesai</span>
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
                        <p class="text-[#9CA3AF] text-sm mt-2">Belum ada pasien yang membuat sesi.</p>
                    </div>
                @endif
            @else
                {{-- PATIENT DASHBOARD --}}
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-[#374151]">Halo, {{ Auth::user()->name }}! 👋</h1>
                    <p class="text-[#6B7280] mt-2">Selamat datang kembali di Hexa Space. Yuk, lihat sesi terakhirmu atau mulai konseling baru.</p>
                </div>

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
                                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-[#FBCFE8] text-[#BE185D]">Active</span>
                                    @else
                                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-[#E5E7EB] text-[#6B7280]">Selesai</span>
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
</x-app-layout>
