<x-app-layout>
    <div class="py-6 bg-[#F8F3FF] min-h-screen">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            @if(auth()->user()->role === 'doctor')
                <a href="{{ route('dashboard') }}" class="text-[#6B7280] hover:text-[#374151] text-sm font-medium transition inline-block mb-4">&larr; Kembali ke Dashboard</a>
            @else
                <a href="{{ route('sessions.index') }}" class="text-[#6B7280] hover:text-[#374151] text-sm font-medium transition inline-block mb-4">&larr; Kembali ke Riwayat</a>
            @endif

            <div class="bg-white rounded-2xl p-6 shadow-sm border border-[#E5E7EB] mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-xl font-bold text-[#374151]">{{ $session->title }}</h1>
                        <p class="text-sm text-[#6B7280] mt-1">
                            {{ $session->counselingService->name ?? 'Layanan' }}
                            @if(auth()->user()->role === 'doctor')
                                &middot; Pasien: <span class="font-medium">{{ $session->user->name }}</span>
                            @endif
                        </p>
                    </div>
                    <div class="flex items-center gap-3">
                        @if($session->status === 'active')
                            <span class="px-4 py-1.5 rounded-full text-xs font-medium bg-purple-100 text-purple-700">Sedang Berlangsung</span>
                        @else
                            <span class="px-4 py-1.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500">Selesai</span>
                        @endif
                    </div>
                </div>
                @if(auth()->user()->role === 'doctor')
                    <div class="mt-3 text-xs text-[#9CA3AF]">
                        Dibuat pada {{ $session->created_at->format('d M Y H:i') }}
                        @if($session->is_escalated)
                            &middot; <span class="text-red-500 font-medium">Butuh Bantuan Ahli</span>
                        @endif
                    </div>
                @endif
            </div>

            @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-700 rounded-2xl px-6 py-4 mb-6">{{ session('error') }}</div>
            @endif

            @if(session('success'))
                <div class="bg-pink-50 border border-pink-200 text-pink-600 rounded-2xl px-6 py-4 mb-6">{{ session('success') }}</div>
            @endif

            @if($session->status === 'finished')
                <div class="bg-purple-50 border border-purple-200 text-purple-700 rounded-2xl px-6 py-5 mb-6 text-center">
                    <div class="text-3xl mb-2">🙏</div>
                    <p class="font-medium">Sesi ini telah berakhir.</p>
                    @if(auth()->user()->role !== 'doctor')
                        <p class="text-sm mt-1">Terima kasih telah berbagi bersama Hexa Space.</p>
                    @endif
                </div>
            @endif

            @if(auth()->user()->role === 'doctor')
                <div class="bg-pink-50 border border-pink-200 text-pink-600 rounded-2xl px-6 py-4 mb-6 text-sm">
                    🔍 Kamu sedang melihat sesi milik <strong>{{ $session->user->name }}</strong> dalam mode baca-saja.
                </div>
            @endif

            <div class="bg-white rounded-2xl shadow-sm border border-[#E5E7EB] p-6 mb-6">
                @if($messages->count() > 0)
                    <div class="space-y-4 max-h-[500px] overflow-y-auto" id="chatMessages">
                        @foreach($messages as $msg)
                            @if($msg->sender === 'user')
                                <div class="flex justify-end">
                                    <div class="max-w-[75%] bg-purple-500 text-white rounded-l-xl rounded-tr-xl px-5 py-3">
                                        <p>{{ $msg->message }}</p>
                                        <p class="text-xs text-purple-200 text-right mt-1">{{ $msg->created_at->format('H:i') }}</p>
                                    </div>
                                </div>
                            @else
                                <div class="flex justify-start">
                                    <div class="max-w-[75%] bg-white border border-purple-100 text-gray-700 rounded-r-xl rounded-tl-xl px-5 py-3 shadow-sm">
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="text-xs font-semibold text-purple-600">Hexa AI</span>
                                        </div>
                                        <p>{{ $msg->message }}</p>
                                        <p class="text-xs text-gray-400 mt-1">{{ $msg->created_at->format('H:i') }}</p>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="text-5xl mb-4">💬</div>
                        <p class="text-[#6B7280] text-lg mb-2">Belum ada pesan di sesi ini.</p>
                        <p class="text-[#9CA3AF] text-sm">Kamu bisa mulai bercerita kapan saja.</p>
                    </div>
                @endif
            </div>

            @if($session->status === 'active' && auth()->user()->role !== 'doctor')
                {{-- Suggestion Chips --}}
                <div class="mb-4 flex flex-wrap gap-2" id="suggestionChips">
                    @if($messages->count() == 0)
                        <button type="button" class="px-4 py-2 rounded-full text-sm bg-pink-50 hover:bg-pink-100 text-pink-600 border border-pink-200 transition" onclick="fillSuggestion(this)">Aku bingung mau mulai cerita dari mana…</button>
                        <button type="button" class="px-4 py-2 rounded-full text-sm bg-pink-50 hover:bg-pink-100 text-pink-600 border border-pink-200 transition" onclick="fillSuggestion(this)">Hari ini rasanya berat sekali…</button>
                    @else
                        <button type="button" class="px-4 py-2 rounded-full text-sm bg-pink-50 hover:bg-pink-100 text-pink-600 border border-pink-200 transition" onclick="fillSuggestion(this)">Aku ingin tahu cara berdamai dengan keadaan ini</button>
                        <button type="button" class="px-4 py-2 rounded-full text-sm bg-pink-50 hover:bg-pink-100 text-pink-600 border border-pink-200 transition" onclick="fillSuggestion(this)">Bagaimana cara menyampaikan hal ini dengan baik?</button>
                    @endif
                </div>

                {{-- Chat Input --}}
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-[#E5E7EB]">
                    <form action="{{ route('chat.store', $session) }}" method="POST">
                        @csrf
                        <div class="flex gap-3">
                            <textarea
                                id="messageInput"
                                name="message"
                                rows="2"
                                placeholder="Ceritakan perasaanmu di sini…"
                                class="flex-1 border border-[#E5E7EB] rounded-2xl px-5 py-3 text-[#374151] placeholder-[#9CA3AF] focus:outline-none focus:ring-2 focus:ring-[#C084FC] focus:border-transparent resize-none"
                                maxlength="1000"
                                required
                            ></textarea>
                            <button type="submit" class="bg-[#C084FC] hover:bg-[#7E22CE] text-white px-6 py-3 rounded-full font-medium transition self-end shadow-sm hover:shadow-md">Kirim</button>
                        </div>
                        @error('message')
                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </form>
                    <div class="flex items-center justify-between mt-3">
                        <p class="text-xs text-[#9CA3AF]">Maksimal 1000 karakter</p>
                        <button type="button" id="triggerFinishModal" class="text-xs text-[#6B7280] hover:text-red-500 transition font-medium">Akhiri Sesi</button>
                    </div>
                </div>
            @endif

            {{-- Doctor Notes --}}
            @if(auth()->user()->role === 'doctor')
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-[#E5E7EB] mt-6">
                    <h3 class="font-semibold text-[#374151] mb-3">Catatan Rekomendasi Klinis</h3>
                    <form action="{{ route('doctor.sessions.notes', $session) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <textarea name="doctor_notes" rows="4" class="w-full border border-[#E5E7EB] rounded-2xl px-5 py-3 text-[#374151] placeholder-[#9CA3AF] focus:outline-none focus:ring-2 focus:ring-[#C084FC] focus:border-transparent resize-none" placeholder="Tulis catatan klinis untuk pasien ini...">{{ old('doctor_notes', $session->doctor_notes) }}</textarea>
                        <div class="mt-3 text-right">
                            <button type="submit" class="bg-[#C084FC] hover:bg-[#7E22CE] text-white px-6 py-2 rounded-full text-sm font-medium transition">Simpan Catatan</button>
                        </div>
                    </form>
                </div>
            @endif
        </div>
    </div>

    {{-- Finish Modal --}}
    <div id="finishModal" class="fixed inset-0 z-50 hidden bg-black/40 flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl p-8 max-w-md w-full shadow-2xl">
            <div class="text-center mb-6">
                <div class="text-4xl mb-3">😊</div>
                <h3 class="text-xl font-bold text-[#374151]">Akhiri Sesi Konseling</h3>
                <p class="text-sm text-[#6B7280] mt-1">Bagaimana kondisi hatimu sekarang?</p>
            </div>
            <form id="finishForm" action="{{ route('sessions.finish', $session) }}" method="POST">
                @csrf
                @method('PATCH')
                <input type="hidden" name="final_mood" id="finalMood" value="">
                <div class="space-y-3">
                    <button type="button" onclick="submitFinish('lebih_tenang')" class="w-full flex items-center gap-4 p-4 border border-[#E5E7EB] rounded-2xl hover:border-purple-300 hover:bg-purple-50 transition text-left">
                        <span class="text-2xl">😊</span>
                        <div>
                            <p class="font-medium text-[#374151]">Lebih Tenang</p>
                            <p class="text-xs text-[#6B7280]">Aku merasa lebih baik setelah bercerita</p>
                        </div>
                    </button>
                    <button type="button" onclick="submitFinish('sama_saja')" class="w-full flex items-center gap-4 p-4 border border-[#E5E7EB] rounded-2xl hover:border-purple-300 hover:bg-purple-50 transition text-left">
                        <span class="text-2xl">😐</span>
                        <div>
                            <p class="font-medium text-[#374151]">Sama Saja</p>
                            <p class="text-xs text-[#6B7280]">Perasaanku masih sama seperti sebelumnya</p>
                        </div>
                    </button>
                    <button type="button" onclick="submitFinish('butuh_bantuan')" class="w-full flex items-center gap-4 p-4 border border-red-200 rounded-2xl hover:border-red-300 hover:bg-red-50 transition text-left">
                        <span class="text-2xl">🙁</span>
                        <div>
                            <p class="font-medium text-red-600">Masih Butuh Bantuan Ahli</p>
                            <p class="text-xs text-[#6B7280]">Aku merasa perlu berkonsultasi lebih lanjut</p>
                        </div>
                    </button>
                </div>
            </form>
            <div class="mt-4 text-center">
                <button type="button" onclick="closeFinishModal()" class="text-sm text-[#6B7280] hover:text-[#374151] transition">Batal</button>
            </div>
        </div>
    </div>

    <script>
        function fillSuggestion(btn) {
            const input = document.getElementById('messageInput');
            if (input) {
                input.value = btn.textContent.trim();
                input.focus();
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            const trigger = document.getElementById('triggerFinishModal');
            if (trigger) {
                trigger.addEventListener('click', function () {
                    document.getElementById('finishModal').classList.remove('hidden');
                });
            }
        });

        function submitFinish(mood) {
            document.getElementById('finalMood').value = mood;
            document.getElementById('finishForm').submit();
        }

        function closeFinishModal() {
            document.getElementById('finishModal').classList.add('hidden');
        }

        const chatContainer = document.getElementById('chatMessages');
        if (chatContainer) {
            chatContainer.scrollTop = chatContainer.scrollHeight;
        }
    </script>
</x-app-layout>
