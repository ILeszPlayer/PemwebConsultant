<x-app-layout>
    <div class="py-12 bg-[#F8F3FF] min-h-screen">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8 flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-[#374151]">Panel Admin Hexa Space</h1>
                    <p class="text-[#6B7280] mt-2">Kelola seluruh aspek aplikasi dari sini.</p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('admin.users.create') }}" class="bg-[#C084FC] hover:bg-[#7E22CE] text-white px-5 py-2.5 rounded-full text-sm font-medium transition shadow-sm">+ Tambah Pengguna</a>
                </div>
            </div>

            <div class="grid md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-[#E5E7EB] hover:shadow-md transition">
                    <div class="flex items-center gap-4">
                        <div class="bg-purple-100 rounded-2xl w-14 h-14 flex items-center justify-center text-2xl">👤</div>
                        <div>
                            <p class="text-sm text-[#6B7280]">Total Pasien</p>
                            <p class="text-3xl font-bold text-[#374151]">{{ $totalUsers }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-[#E5E7EB] hover:shadow-md transition">
                    <div class="flex items-center gap-4">
                        <div class="bg-sky-100 rounded-2xl w-14 h-14 flex items-center justify-center text-2xl">🩺</div>
                        <div>
                            <p class="text-sm text-[#6B7280]">Total Dokter</p>
                            <p class="text-3xl font-bold text-[#374151]">{{ $totalDoctors }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-[#E5E7EB] hover:shadow-md transition">
                    <div class="flex items-center gap-4">
                        <div class="bg-amber-100 rounded-2xl w-14 h-14 flex items-center justify-center text-2xl">🔐</div>
                        <div>
                            <p class="text-sm text-[#6B7280]">Total Admin</p>
                            <p class="text-3xl font-bold text-[#374151]">{{ $totalAdmins }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-[#E5E7EB] hover:shadow-md transition">
                    <div class="flex items-center gap-4">
                        <div class="bg-rose-100 rounded-2xl w-14 h-14 flex items-center justify-center text-2xl">🚫</div>
                        <div>
                            <p class="text-sm text-[#6B7280]">Akun Diblokir</p>
                            <p class="text-3xl font-bold text-[#374151]">{{ $bannedUsers }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Session Activity Chart --}}
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-[#E5E7EB] mb-8">
                <h2 class="text-lg font-semibold text-[#374151] mb-4">📈 Aktivitas Sesi (7 Hari)</h2>
                <div class="flex items-end gap-3 h-32">
                    @php $maxVal = max(max($chartData), 1); @endphp
                    @foreach($chartData as $i => $val)
                        <div class="flex-1 flex flex-col items-center gap-2">
                            <span class="text-xs font-bold text-[#7E22CE]">{{ $val }}</span>
                            <div class="w-full bg-purple-100 rounded-lg overflow-hidden" style="height: 80px;">
                                <div class="w-full bg-gradient-to-t from-[#C084FC] to-[#7E22CE] rounded-lg transition-all duration-500" style="height: {{ ($val / $maxVal) * 100 }}%;"></div>
                            </div>
                            <span class="text-[10px] text-gray-400 font-medium">{{ $chartLabels[$i] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Activity Stats --}}
            <div class="grid md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-[#E5E7EB]">
                    <div class="flex items-center gap-4">
                        <div class="bg-green-100 rounded-2xl w-14 h-14 flex items-center justify-center text-2xl">💬</div>
                        <div>
                            <p class="text-sm text-[#6B7280]">Sesi Aktif</p>
                            <p class="text-3xl font-bold text-[#374151]">{{ $activeSessions }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-[#E5E7EB]">
                    <div class="flex items-center gap-4">
                        <div class="bg-indigo-100 rounded-2xl w-14 h-14 flex items-center justify-center text-2xl">📋</div>
                        <div>
                            <p class="text-sm text-[#6B7280]">Total Sesi</p>
                            <p class="text-3xl font-bold text-[#374151]">{{ $totalSessions }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-[#E5E7EB]">
                    <div class="flex items-center gap-4">
                        <div class="bg-red-100 rounded-2xl w-14 h-14 flex items-center justify-center text-2xl">🚨</div>
                        <div>
                            <p class="text-sm text-[#6B7280]">Butuh Eskalasi</p>
                            <p class="text-3xl font-bold text-[#374151]">{{ $escalatedSessions }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-[#E5E7EB]">
                    <div class="flex items-center gap-4">
                        <div class="bg-orange-100 rounded-2xl w-14 h-14 flex items-center justify-center text-2xl">📝</div>
                        <div>
                            <p class="text-sm text-[#6B7280]">Total Jurnal</p>
                            <p class="text-3xl font-bold text-[#374151]">{{ $totalJournals }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Management Links --}}
            <div class="grid md:grid-cols-3 gap-6 mb-8">
                <a href="{{ route('admin.users.index') }}" class="bg-white rounded-2xl p-8 shadow-sm border border-[#E5E7EB] text-center hover:shadow-md transition group">
                    <div class="text-5xl mb-4 group-hover:scale-110 transition-transform">👥</div>
                    <h2 class="text-lg font-semibold text-[#374151] mb-2">Manajemen Pengguna</h2>
                    <p class="text-[#6B7280] text-sm mb-4">Kelola data pasien dan dokter, ubah password, atau hapus akun.</p>
                    <span class="inline-block text-[#C084FC] font-medium text-sm group-hover:text-[#7E22CE] transition">Kelola Pengguna &rarr;</span>
                </a>
                <a href="{{ route('admin.articles.index') }}" class="bg-white rounded-2xl p-8 shadow-sm border border-[#E5E7EB] text-center hover:shadow-md transition group">
                    <div class="text-5xl mb-4 group-hover:scale-110 transition-transform">📚</div>
                    <h2 class="text-lg font-semibold text-[#374151] mb-2">Manajemen Artikel</h2>
                    <p class="text-[#6B7280] text-sm mb-4">Buat dan kelola artikel kesehatan mental. ({{ $totalArticles }} artikel)</p>
                    <span class="inline-block text-[#C084FC] font-medium text-sm group-hover:text-[#7E22CE] transition">Kelola Artikel &rarr;</span>
                </a>
                <a href="{{ route('admin.services.index') }}" class="bg-white rounded-2xl p-8 shadow-sm border border-[#E5E7EB] text-center hover:shadow-md transition group">
                    <div class="text-5xl mb-4 group-hover:scale-110 transition-transform">🛠️</div>
                    <h2 class="text-lg font-semibold text-[#374151] mb-2">Manajemen Layanan</h2>
                    <p class="text-[#6B7280] text-sm mb-4">Atur layanan konseling yang tersedia untuk pasien.</p>
                    <span class="inline-block text-[#C084FC] font-medium text-sm group-hover:text-[#7E22CE] transition">Kelola Layanan &rarr;</span>
                </a>
            </div>

            {{-- Activity Feed --}}
            <div class="grid md:grid-cols-2 gap-6">
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-[#E5E7EB]">
                    <h3 class="text-sm font-semibold text-[#374151] mb-4">💬 Pesan Terbaru</h3>
                    @if($recentMessages->count() > 0)
                        <div class="space-y-3">
                            @foreach($recentMessages as $msg)
                                <div class="flex items-start gap-3 p-3 bg-gray-50 rounded-xl">
                                    <div class="w-8 h-8 rounded-full bg-purple-100 flex items-center justify-center text-sm shrink-0">
                                        {{ $msg->counselingSession->user->name[0] ?? '?' }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-xs font-semibold text-[#374151] truncate">{{ $msg->counselingSession->user->name ?? 'Unknown' }}</p>
                                        <p class="text-[11px] text-gray-500 truncate">{{ $msg->message }}</p>
                                        <p class="text-[10px] text-gray-400 mt-0.5">{{ $msg->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-400 text-center py-6">Belum ada pesan.</p>
                    @endif
                </div>
                <div class="space-y-6">
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-[#E5E7EB]">
                        <h3 class="text-sm font-semibold text-[#374151] mb-4">📝 Jurnal Terbaru</h3>
                        @if($recentJournals->count() > 0)
                            <div class="space-y-3">
                                @foreach($recentJournals as $j)
                                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                                        <span class="text-xl">{{ $j->mood_emoji }}</span>
                                        <div class="min-w-0">
                                            <p class="text-xs font-semibold text-[#374151]">{{ $j->user->name }}</p>
                                            <p class="text-[11px] text-gray-500 truncate">{{ $j->note ?? '—' }}</p>
                                            <p class="text-[10px] text-gray-400 mt-0.5">{{ $j->created_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-gray-400 text-center py-6">Belum ada jurnal.</p>
                        @endif
                    </div>
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-[#E5E7EB]">
                        <h3 class="text-sm font-semibold text-[#374151] mb-4">👤 Pengguna Baru</h3>
                        @if($recentUsers->count() > 0)
                            <div class="space-y-2">
                                @foreach($recentUsers as $u)
                                    <div class="flex items-center gap-3 p-2">
                                        <div class="w-8 h-8 rounded-full bg-amber-100 flex items-center justify-center text-sm">{{ $u->name[0] }}</div>
                                        <div>
                                            <p class="text-sm font-medium text-[#374151]">{{ $u->name }}</p>
                                            <p class="text-[10px] text-gray-400">{{ $u->created_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-gray-400 text-center py-6">Belum ada pengguna baru.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
