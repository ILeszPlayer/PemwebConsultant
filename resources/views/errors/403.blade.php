<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>403 - Akses Ditolak - Hexa Space</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css'])
</head>
<body class="font-sans antialiased bg-[#F8F3FF] min-h-screen flex items-center justify-center">
    <div class="text-center max-w-md mx-auto px-6">
        <div class="text-8xl mb-6">🚫</div>
        <h1 class="text-4xl font-bold text-[#374151] mb-3">Akses Ditolak</h1>
        <p class="text-[#6B7280] mb-8">{{ $exception->getMessage() ?: 'Kamu tidak memiliki izin untuk mengakses halaman ini.' }}</p>
        <div class="flex flex-wrap justify-center gap-4">
            <a href="{{ url()->previous() !== url()->current() ? url()->previous() : url('/') }}" class="inline-block bg-[#C084FC] hover:bg-[#7E22CE] text-white px-6 py-3 rounded-full font-medium transition shadow-sm">&larr; Kembali</a>
            <a href="{{ url('/') }}" class="inline-block bg-white border border-[#E5E7EB] hover:bg-gray-50 text-[#374151] px-6 py-3 rounded-full font-medium transition shadow-sm">Ke Beranda</a>
        </div>
    </div>
</body>
</html>
