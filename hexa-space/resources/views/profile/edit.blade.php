<x-app-layout>
    <div class="py-12 bg-[#F8F3FF] min-h-screen">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Stats Card --}}
            @if(auth()->user()->role === 'user')
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-[#E5E7EB]">
                <h2 class="text-lg font-semibold text-[#374151] mb-4">📊 Ringkasan Aktivitas</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="text-center p-4 bg-purple-50 rounded-2xl">
                        <p class="text-2xl font-bold text-[#7E22CE]">{{ $totalSessions ?? 0 }}</p>
                        <p class="text-xs text-gray-500">Total Sesi</p>
                    </div>
                    <div class="text-center p-4 bg-rose-50 rounded-2xl">
                        <p class="text-2xl font-bold text-rose-600">{{ $activeSessions ?? 0 }}</p>
                        <p class="text-xs text-gray-500">Sesi Aktif</p>
                    </div>
                    <div class="text-center p-4 bg-emerald-50 rounded-2xl">
                        <p class="text-2xl font-bold text-emerald-600">{{ $totalJournals ?? 0 }}</p>
                        <p class="text-xs text-gray-500">Jurnal</p>
                    </div>
                    <div class="text-center p-4 bg-amber-50 rounded-2xl">
                        <p class="text-2xl font-bold text-amber-600">🔥 {{ $streak ?? 0 }}</p>
                        <p class="text-xs text-gray-500">Streak Hari</p>
                    </div>
                </div>
            </div>
            @endif

            {{-- Avatar --}}
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-[#E5E7EB]">
                <section>
                    <header>
                        <h2 class="text-lg font-semibold text-[#374151]">Foto Profil</h2>
                        <p class="mt-1 text-sm text-[#6B7280]">Upload foto profil untuk personalisasi akunmu.</p>
                    </header>

                    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="mt-6 space-y-4">
                        @csrf @method('patch')

                        <div class="flex items-center gap-6">
                            <div class="relative w-20 h-20 rounded-full overflow-hidden bg-[#F8F3FF] border-2 border-[#C084FC]">
                                <img id="avatarPreview" src="{{ Auth::user()->avatar ? Storage::url(Auth::user()->avatar) : '' }}" class="w-full h-full object-cover {{ Auth::user()->avatar ? '' : 'hidden' }}">
                                <div id="avatarPlaceholder" class="w-full h-full flex items-center justify-center text-3xl {{ Auth::user()->avatar ? 'hidden' : '' }}">👤</div>
                            </div>
                            <div>
                                <input type="file" name="avatar" id="avatarInput" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-[#F8F3FF] file:text-[#7E22CE] hover:file:bg-[#E9D5FF]">
                                @error('avatar') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="flex items-center gap-4">
                            <button type="submit" class="bg-[#C084FC] hover:bg-[#7E22CE] text-white px-6 py-2 rounded-full text-sm font-medium transition">Simpan Foto</button>
                            @if(Auth::user()->avatar)
                                <a href="{{ route('profile.avatar.remove') }}" class="text-red-500 hover:text-red-700 text-sm font-medium transition" onclick="return confirm('Hapus foto profil?')">Hapus Foto</a>
                            @endif
                        </div>
                    </form>
                </section>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-sm border border-[#E5E7EB]">
                @include('profile.partials.update-profile-information-form')
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-sm border border-[#E5E7EB]">
                @include('profile.partials.update-password-form')
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-sm border border-[#E5E7EB]">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>

    <script>
        document.getElementById('avatarInput')?.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = function(ev) {
                const img = document.getElementById('avatarPreview');
                const placeholder = document.getElementById('avatarPlaceholder');
                img.src = ev.target.result;
                img.classList.remove('hidden');
                placeholder.classList.add('hidden');
            };
            reader.readAsDataURL(file);
        });
    </script>
</x-app-layout>
