<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Hexa Space - {{ $title ?? 'Ruang Nyaman untuk Pulih dan Bertumbuh' }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <style>
            [x-cloak] { display: none !important; }
        </style>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" x-init="document.documentElement.classList.toggle('dark', darkMode); $watch('darkMode', val => document.documentElement.classList.toggle('dark', val))" x-bind:class="darkMode ? 'bg-gray-900' : 'bg-[#F8F3FF]'" class="min-h-screen">
            @include('layouts.navigation')

            @isset($header)
                <header class="shadow-sm border-b" :class="darkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-[#E5E7EB]'">
                    <div class="max-w-6xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <main>
                {{ $slot }}
            </main>
        </div>

        {{-- Toast Notifications --}}
        @if(session('success'))
            <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 4000)" x-show="show" x-cloak class="fixed top-4 right-4 z-50 bg-emerald-500 text-white px-6 py-3 rounded-2xl shadow-lg flex items-center gap-3 text-sm font-medium transition-all duration-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <span>{{ session('success') }}</span>
                <button @click="show = false" class="ml-2 text-white/80 hover:text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 4000)" x-show="show" x-cloak class="fixed top-4 right-4 z-50 bg-red-500 text-white px-6 py-3 rounded-2xl shadow-lg flex items-center gap-3 text-sm font-medium transition-all duration-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <span>{{ session('error') }}</span>
                <button @click="show = false" class="ml-2 text-white/80 hover:text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
        @endif

        {{-- Dark Mode Styles --}}
        <style>
            .dark body { background-color: #0f172a !important; color: #e2e8f0 !important; }
            .dark .bg-white { background-color: #1e293b !important; }
            .dark .bg-\[#F8F3FF\] { background-color: #0f172a !important; }
            .dark .bg-\[#F8F3FF\]\/40 { background-color: rgba(15, 23, 42, 0.4) !important; }
            .dark .bg-\[#F8F3FF\]\/30 { background-color: rgba(15, 23, 42, 0.3) !important; }
            .dark .bg-\[#F8F3FF\]\/50 { background-color: rgba(30, 41, 59, 0.5) !important; }
            .dark .border-\[#E5E7EB\] { border-color: #334155 !important; }
            .dark .border-purple-100 { border-color: #334155 !important; }
            .dark .border-purple-200 { border-color: #4c1d95 !important; }
            .dark .border-rose-200 { border-color: #9f1239 !important; }
            .dark .border-rose-100 { border-color: #4c0519 !important; }
            .dark .border-sky-200 { border-color: #075985 !important; }
            .dark .border-emerald-200 { border-color: #065f46 !important; }
            .dark .border-\[\#F3F4F6\] { border-color: #1e293b !important; }
            .dark .text-\[#374151\] { color: #e2e8f0 !important; }
            .dark .text-\[#6B7280\] { color: #94a3b8 !important; }
            .dark .text-\[#9CA3AF\] { color: #64748b !important; }
            .dark .text-\[#BE185D\] { color: #f472b6 !important; }
            .dark .text-gray-800 { color: #e2e8f0 !important; }
            .dark .text-gray-700 { color: #cbd5e1 !important; }
            .dark .text-gray-500 { color: #94a3b8 !important; }
            .dark .text-gray-400 { color: #64748b !important; }
            .dark .hover\:text-gray-700:hover { color: #e2e8f0 !important; }
            .dark .hover\:text-\[#374151\]:hover { color: #e2e8f0 !important; }
            .dark .bg-gray-100 { background-color: #334155 !important; }
            .dark .hover\:bg-gray-200:hover { background-color: #475569 !important; }
            .dark .divide-\[\#E5E7EB\] > * { border-color: #334155 !important; }
            .dark .shadow-sm { box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.4) !important; }
            .dark .shadow-md { box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.4) !important; }
            .dark .shadow-lg { box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.4) !important; }
            .dark .shadow-xl { box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.4) !important; }
            .dark input, .dark textarea, .dark select { background-color: #1e293b !important; color: #e2e8f0 !important; border-color: #334155 !important; }
            .dark input:focus, .dark textarea:focus, .dark select:focus { border-color: #a78bfa !important; --tw-ring-color: #a78bfa !important; }
            .dark .placeholder-\[\#9CA3AF\]::placeholder { color: #475569 !important; }
            .dark .line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
            .dark .bg-rose-50 { background-color: #4c0519 !important; }
            .dark .bg-purple-50 { background-color: #2e1065 !important; }
            .dark .bg-sky-50 { background-color: #082f49 !important; }
            .dark .bg-emerald-50 { background-color: #022c22 !important; }
            .dark .bg-red-50 { background-color: #450a0a !important; }
            .dark .text-rose-700 { color: #fb7185 !important; }
            .dark .text-rose-600 { color: #f43f5e !important; }
            .dark .text-purple-700 { color: #c084fc !important; }
            .dark .text-purple-600 { color: #a78bfa !important; }
            .dark .text-sky-700 { color: #38bdf8 !important; }
            .dark .text-sky-600 { color: #0ea5e9 !important; }
            .dark .text-emerald-700 { color: #34d399 !important; }
            .dark .text-emerald-600 { color: #10b981 !important; }
            .dark .text-red-700 { color: #fca5a5 !important; }
            .dark .text-rose-500 { color: #e11d48 !important; }
            .dark .hover\:bg-rose-600:hover { background-color: #e11d48 !important; }
            .dark .hover\:bg-purple-600:hover { background-color: #9333ea !important; }
            .dark .hover\:bg-sky-600:hover { background-color: #0284c7 !important; }
            .dark .hover\:bg-emerald-600:hover { background-color: #059669 !important; }
            .dark .bg-gradient-to-r { opacity: 0.8; }
            .dark .hover\:shadow-lg:hover { box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.5) !important; }
            .dark .hover\:shadow-md:hover { box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.4) !important; }
            .dark a { color: #a78bfa; }
            .dark a:hover { color: #c084fc; }
            .dark .prose { color: #e2e8f0 !important; }
            .dark .prose p { color: #cbd5e1 !important; }
            .dark table th { color: #94a3b8 !important; }
            .dark table td { color: #cbd5e1 !important; }
            .dark .hover\:bg-\[#F8F3FF\]\/50:hover { background-color: rgba(30, 41, 59, 0.5) !important; }
            .dark .hover\:border-\[#C084FC\]:hover { border-color: #a78bfa !important; }
            .dark .bg-\[\#FCE7F3\] { background-color: #4c0519 !important; border-color: #9f1239 !important; }
            .dark .border-\[\#FBCFE8\] { border-color: #9f1239 !important; }
            .dark .bg-white\/80 { background-color: rgba(30, 41, 59, 0.8) !important; }
            .dark .backdrop-blur-sm { backdrop-filter: blur(4px); }
        </style>
    </body>
</html>
