<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Hexa Space - Ruang Nyaman untuk Pulih dan Bertumbuh</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-[#F8F3FF] text-[#374151]">
    <header class="bg-white/80 backdrop-blur-sm border-b border-[#E5E7EB] sticky top-0 z-50">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <a href="/" class="flex items-center gap-2">
                    <span class="text-2xl">🫧</span>
                    <span class="font-bold text-xl text-[#7E22CE]">Hexa Space</span>
                </a>
                <nav class="flex items-center gap-4">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="bg-[#C084FC] hover:bg-[#7E22CE] text-white px-5 py-2 rounded-full text-sm font-medium transition">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="text-[#6B7280] hover:text-[#374151] text-sm font-medium transition">Masuk</a>
                        <a href="{{ route('register') }}" class="bg-[#C084FC] hover:bg-[#7E22CE] text-white px-5 py-2 rounded-full text-sm font-medium transition">Daftar</a>
                    @endauth
                </nav>
            </div>
        </div>
    </header>

    <main>
        {{-- Hero --}}
        <section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-20 md:py-32">
            <div class="text-center max-w-3xl mx-auto">
                <h1 class="text-4xl md:text-5xl font-bold text-[#374151] leading-tight mb-6">
                    Lagi capek sama hidup? <span class="text-[#7E22CE]">Yuk, cerita dulu.</span>
                </h1>
                <p class="text-lg md:text-xl text-[#6B7280] leading-relaxed mb-8">
                    Kadang isi kepala terasa penuh dan hidup jadi melelahkan. Tenang, kamu nggak harus menghadapi semuanya sendiri.
                    Hexa Space hadir sebagai ruang aman untuk bercerita bersama konselor berbasis AI yang siap menemani kamu kapan saja.
                </p>
                <div class="flex flex-wrap justify-center gap-4 mb-10">
                    <span class="text-[#6B7280] text-sm font-medium">Aman</span>
                    <span class="text-[#C084FC]">•</span>
                    <span class="text-[#6B7280] text-sm font-medium">Nyaman</span>
                    <span class="text-[#C084FC]">•</span>
                    <span class="text-[#6B7280] text-sm font-medium">24 Jam</span>
                    <span class="text-[#C084FC]">•</span>
                    <span class="text-[#6B7280] text-sm font-medium">Rahasia Terjaga</span>
                </div>
                @auth
                    <a href="{{ route('services.index') }}" class="inline-block bg-[#C084FC] hover:bg-[#7E22CE] text-white px-8 py-3 rounded-full text-lg font-medium transition shadow-md hover:shadow-lg">Mulai Konseling</a>
                @else
                    <a href="{{ route('register') }}" class="inline-block bg-[#C084FC] hover:bg-[#7E22CE] text-white px-8 py-3 rounded-full text-lg font-medium transition shadow-md hover:shadow-lg">Mulai Sekarang</a>
                @endauth
            </div>
        </section>

        {{-- Apa Itu Konseling --}}
        <section class="bg-white py-20">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-3xl mx-auto">
                    <h2 class="text-3xl font-bold text-[#374151] mb-6">Apa Itu Konseling?</h2>
                    <p class="text-lg text-[#6B7280] leading-relaxed">
                        Konseling adalah ruang aman di mana kamu bisa bercerita, mengeksplorasi perasaan, dan menemukan cara menghadapi tantangan hidup. Di Hexa Space, kamu bisa bercerita tanpa takut dihakimi — didampingi oleh AI yang siap mendengarkan dengan hangat dan empati.
                    </p>
                </div>
            </div>
        </section>

        {{-- Kenapa Harus Menggunakan Hexa Space --}}
        <section class="py-20">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-3xl font-bold text-center text-[#374151] mb-12">Kenapa Harus Menggunakan Hexa Space?</h2>
                <div class="grid md:grid-cols-3 gap-8">
                    <div class="bg-white rounded-2xl p-8 shadow-sm border border-[#E5E7EB] text-center">
                        <div class="text-4xl mb-4">🎯</div>
                        <h3 class="font-semibold text-lg text-[#374151] mb-2">Mudah Diakses</h3>
                        <p class="text-[#6B7280] leading-relaxed">Kamu bisa bercerita kapan saja dan di mana saja tanpa perlu janji temu.</p>
                    </div>
                    <div class="bg-white rounded-2xl p-8 shadow-sm border border-[#E5E7EB] text-center">
                        <div class="text-4xl mb-4">🤖</div>
                        <h3 class="font-semibold text-lg text-[#374151] mb-2">AI yang Empatik</h3>
                        <p class="text-[#6B7280] leading-relaxed">Ditemani AI dummy yang dirancang untuk mendengarkan dan memberi respons hangat.</p>
                    </div>
                    <div class="bg-white rounded-2xl p-8 shadow-sm border border-[#E5E7EB] text-center">
                        <div class="text-4xl mb-4">🔒</div>
                        <h3 class="font-semibold text-lg text-[#374151] mb-2">Privasi Terjaga</h3>
                        <p class="text-[#6B7280] leading-relaxed">Semua ceritamu bersifat rahasia dan hanya kamu yang bisa mengaksesnya.</p>
                    </div>
                </div>
            </div>
        </section>

        {{-- Bagaimana Hexa Space Membantu Klien --}}
        <section class="bg-white py-20">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-3xl font-bold text-center text-[#374151] mb-12">Bagaimana Hexa Space Membantu Klien?</h2>
                <div class="max-w-4xl mx-auto space-y-6">
                    <div class="flex gap-4 items-start bg-[#F8F3FF] rounded-2xl p-6">
                        <div class="bg-[#E9D5FF] rounded-full w-10 h-10 flex items-center justify-center shrink-0 text-lg">1</div>
                        <div>
                            <h3 class="font-semibold text-[#374151] mb-1">Pilih Layanan</h3>
                            <p class="text-[#6B7280]">Tentukan jenis konseling yang sesuai dengan kebutuhanmu.</p>
                        </div>
                    </div>
                    <div class="flex gap-4 items-start bg-[#F8F3FF] rounded-2xl p-6">
                        <div class="bg-[#E9D5FF] rounded-full w-10 h-10 flex items-center justify-center shrink-0 text-lg">2</div>
                        <div>
                            <h3 class="font-semibold text-[#374151] mb-1">Mulai Sesi</h3>
                            <p class="text-[#6B7280]">Buat sesi konseling baru dan masuk ke ruang chat yang aman.</p>
                        </div>
                    </div>
                    <div class="flex gap-4 items-start bg-[#F8F3FF] rounded-2xl p-6">
                        <div class="bg-[#E9D5FF] rounded-full w-10 h-10 flex items-center justify-center shrink-0 text-lg">3</div>
                        <div>
                            <h3 class="font-semibold text-[#374151] mb-1">Cerita Bebas</h3>
                            <p class="text-[#6B7280]">Curhat dan berceritalah dengan bebas. AI akan merespons dengan hangat dan empati.</p>
                        </div>
                    </div>
                    <div class="flex gap-4 items-start bg-[#F8F3FF] rounded-2xl p-6">
                        <div class="bg-[#E9D5FF] rounded-full w-10 h-10 flex items-center justify-center shrink-0 text-lg">4</div>
                        <div>
                            <h3 class="font-semibold text-[#374151] mb-1">Lihat Perkembangan</h3>
                            <p class="text-[#6B7280]">Pantau riwayat sesi konselingmu dan lihat sejauh mana perjalananmu.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Jenis Layanan Konseling --}}
        <section class="py-20">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-3xl font-bold text-center text-[#374151] mb-4">Jenis Layanan Konseling</h2>
                <p class="text-center text-[#6B7280] mb-12 max-w-2xl mx-auto">Pilih layanan yang paling sesuai dengan kebutuhanmu saat ini.</p>
                @if($services->count() > 0)
                    <div class="grid md:grid-cols-3 gap-8">
                        @foreach($services as $service)
                            <div class="bg-white rounded-2xl p-8 shadow-sm border border-[#E5E7EB] hover:shadow-md transition">
                                <div class="text-5xl mb-5">{{ $service->icon ?? '💬' }}</div>
                                <h3 class="font-semibold text-xl text-[#374151] mb-3">{{ $service->name }}</h3>
                                <p class="text-[#6B7280] leading-relaxed mb-6">{{ $service->description }}</p>
                                @auth
                                    <form action="{{ route('sessions.store') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="service_id" value="{{ $service->id }}">
                                        <button type="submit" class="w-full bg-[#C084FC] hover:bg-[#7E22CE] text-white px-6 py-3 rounded-full font-medium transition text-sm">Pilih Layanan</button>
                                    </form>
                                @else
                                    <a href="{{ route('register') }}" class="block text-center bg-[#C084FC] hover:bg-[#7E22CE] text-white px-6 py-3 rounded-full font-medium transition text-sm">Daftar untuk Memulai</a>
                                @endauth
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12 bg-white rounded-2xl border border-[#E5E7EB]">
                        <p class="text-[#6B7280]">Belum ada layanan tersedia saat ini.</p>
                    </div>
                @endif
            </div>
        </section>
    </main>

    <footer class="bg-white border-t border-[#E5E7EB] py-10">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="flex items-center justify-center gap-2 mb-3">
                <span class="text-xl">🫧</span>
                <span class="font-bold text-lg text-[#7E22CE]">Hexa Space</span>
            </div>
            <p class="text-[#6B7280] text-sm">Ruang Nyaman untuk Pulih dan Bertumbuh</p>
            <p class="text-[#9CA3AF] text-xs mt-4">&copy; {{ date('Y') }} Hexa Space. Dibuat dengan penuh empati.</p>
        </div>
    </footer>
</body>
</html>
