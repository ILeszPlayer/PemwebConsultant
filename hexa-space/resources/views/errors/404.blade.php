<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 - Halaman Tidak Ditemukan - Hexa Space</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css'])
    <style>
        .float-anim { animation: float 3s ease-in-out infinite; }
        @keyframes float { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-12px); } }
    </style>
</head>
<body class="font-sans antialiased bg-[#F8F3FF] min-h-screen flex items-center justify-center">
    <div class="text-center max-w-md mx-auto px-6">
        <div class="text-8xl mb-6 float-anim">🌙</div>
        <h1 class="text-4xl font-bold text-[#374151] mb-3">Halaman Tidak Ditemukan</h1>
        <p class="text-[#6B7280] mb-2">Sepertinya kamu tersesat. Halaman yang kamu cari tidak ada atau telah dipindahkan.</p>
        <p class="text-sm text-[#9CA3AF] mb-8">Jangan khawatir, kamu bisa kembali ke tempat yang aman.</p>
        <div class="flex flex-wrap justify-center gap-4">
            <a href="{{ url()->previous() !== url()->current() ? url()->previous() : url('/') }}" class="inline-flex items-center gap-2 bg-[#C084FC] hover:bg-[#7E22CE] text-white px-6 py-3 rounded-full font-medium transition shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                Kembali
            </a>
            <a href="{{ url('/') }}" class="inline-flex items-center gap-2 bg-white border border-[#E5E7EB] hover:bg-gray-50 text-[#374151] px-6 py-3 rounded-full font-medium transition shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                Ke Beranda
            </a>
        </div>
    </div>
</body>
</html>
