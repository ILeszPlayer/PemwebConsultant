<x-app-layout>
    <div class="py-6 bg-[#F8F3FF] min-h-screen">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Back --}}
            @if(auth()->user()->role === 'doctor')
                <a href="{{ route('dashboard') }}" class="text-[#6B7280] hover:text-[#374151] text-sm font-medium transition inline-block mb-4">&larr; Kembali ke Dashboard</a>
            @else
                <a href="{{ route('sessions.index') }}" class="text-[#6B7280] hover:text-[#374151] text-sm font-medium transition inline-block mb-4">&larr; Kembali ke Riwayat</a>
            @endif

            {{-- Header --}}
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
                            <span class="px-4 py-1.5 rounded-full text-xs font-medium bg-[#FBCFE8] text-[#BE185D]">Sedang Berlangsung</span>
                        @else
                            <span class="px-4 py-1.5 rounded-full text-xs font-medium bg-[#E5E7EB] text-[#6B7280]">Selesai</span>
                        @endif
                    </div>
                </div>
                @if(auth()->user()->role === 'doctor')
                    <div class="mt-3 text-xs text-[#9CA3AF]">
                        Dibuat pada {{ $session->created_at->format('d M Y H:i') }}
                    </div>
                @endif
            </div>

            {{-- Alert --}}
            @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-700 rounded-2xl px-6 py-4 mb-6">
                    {{ session('error') }}
                </div>
            @endif

            @if(session('success'))
                <div class="bg-[#FCE7F3] border border-[#FBCFE8] text-[#BE185D] rounded-2xl px-6 py-4 mb-6">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Sesi Selesai Alert --}}
            @if($session->status === 'finished')
                <div class="bg-[#E9D5FF] border border-[#C084FC] text-[#6B21A8] rounded-2xl px-6 py-5 mb-6 text-center">
                    <div class="text-3xl mb-2">🙏</div>
                    <p class="font-medium">Sesi ini telah berakhir.</p>
                    @if(auth()->user()->role !== 'doctor')
                        <p class="text-sm mt-1">Terima kasih telah berbagi bersama Hexa Space.</p>
                    @endif
                </div>
            @endif

            {{-- Doctor read-only banner --}}
            @if(auth()->user()->role === 'doctor')
                <div class="bg-[#FCE7F3] border border-[#FBCFE8] text-[#BE185D] rounded-2xl px-6 py-4 mb-6 text-sm">
                    🔍 Kamu sedang melihat sesi milik <strong>{{ $session->user->name }}</strong> dalam mode baca-saja.
                </div>
            @endif

            {{-- Chat Messages --}}
            <div class="bg-white rounded-2xl shadow-sm border border-[#E5E7EB] p-6 mb-6">
                @if($messages->count() > 0)
                    <div class="space-y-4 max-h-[500px] overflow-y-auto">
                        @foreach($messages as $msg)
                            @if($msg->sender === 'user')
                                <div class="flex justify-end">
                                    <div class="max-w-[75%] bg-[#E9D5FF] rounded-2xl rounded-br-sm px-5 py-3">
                                        <p class="text-[#374151]">{{ $msg->message }}</p>
                                        <p class="text-xs text-[#6B7280] text-right mt-1">{{ $msg->created_at->format('H:i') }}</p>
                                    </div>
                                </div>
                            @else
                                <div class="flex justify-start">
                                    <div class="max-w-[75%] bg-[#FCE7F3] rounded-2xl rounded-bl-sm px-5 py-3">
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="text-xs font-medium text-[#BE185D]">Hexa AI</span>
                                        </div>
                                        <p class="text-[#374151]">{{ $msg->message }}</p>
                                        <p class="text-xs text-[#6B7280] mt-1">{{ $msg->created_at->format('H:i') }}</p>
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

            {{-- Chat Input --}}
            @if($session->status === 'active' && auth()->user()->role !== 'doctor')
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-[#E5E7EB]">
                    <form action="{{ route('chat.store', $session) }}" method="POST">
                        @csrf
                        <div class="flex gap-3">
                            <textarea
                                name="message"
                                rows="2"
                                placeholder="Ceritakan perasaanmu di sini..."
                                class="flex-1 border border-[#E5E7EB] rounded-2xl px-5 py-3 text-[#374151] placeholder-[#9CA3AF] focus:outline-none focus:ring-2 focus:ring-[#C084FC] focus:border-transparent resize-none"
                                maxlength="1000"
                                required
                            ></textarea>
                            <button type="submit" class="bg-[#C084FC] hover:bg-[#7E22CE] text-white px-6 py-3 rounded-full font-medium transition self-end shadow-sm hover:shadow-md">
                                Kirim
                            </button>
                        </div>
                        @error('message')
                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </form>
                    <div class="flex items-center justify-between mt-3">
                        <p class="text-xs text-[#9CA3AF]">Maksimal 1000 karakter</p>
                        <form action="{{ route('sessions.finish', $session) }}" method="POST" onsubmit="return confirm('Yakin ingin mengakhiri sesi ini?')">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="text-xs text-[#6B7280] hover:text-red-500 transition font-medium">Akhiri Sesi</button>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
