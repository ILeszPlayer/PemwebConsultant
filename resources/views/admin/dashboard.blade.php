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
            <div class="grid md:grid-cols-3 gap-6">
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
        </div>
    </div>
</x-app-layout>
