<x-app-layout>
    <div class="py-12 bg-[#F8F3FF] min-h-screen">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8 text-center">
                <h1 class="text-4xl font-bold text-[#374151]">🆘 Butuh Bantuan Segera?</h1>
                <p class="text-[#6B7280] mt-2">Kamu tidak sendirian. Hubungi salah satu kontak darurat di bawah ini.</p>
            </div>

            <div class="grid md:grid-cols-2 gap-6 mb-8">
                <div class="bg-rose-50 rounded-3xl p-6 border border-rose-200 shadow-sm">
                    <div class="text-4xl mb-3">📞</div>
                    <h3 class="text-lg font-bold text-rose-700">Hotline Kesehatan Jiwa</h3>
                    <p class="text-sm text-rose-600 mt-1">Kementerian Kesehatan RI</p>
                    <a href="tel:119" class="inline-block mt-3 bg-rose-500 hover:bg-rose-600 text-white px-6 py-3 rounded-full font-bold text-lg transition shadow-md">119 (Ekstensi 8)</a>
                </div>

                <div class="bg-purple-50 rounded-3xl p-6 border border-purple-200 shadow-sm">
                    <div class="text-4xl mb-3">💜</div>
                    <h3 class="text-lg font-bold text-purple-700">Yayasan Pulih</h3>
                    <p class="text-sm text-purple-600 mt-1">Pendampingan psikososial</p>
                    <a href="tel:02178842599" class="inline-block mt-3 bg-purple-500 hover:bg-purple-600 text-white px-6 py-3 rounded-full font-bold text-lg transition shadow-md">(021) 7884-2599</a>
                </div>

                <div class="bg-sky-50 rounded-3xl p-6 border border-sky-200 shadow-sm">
                    <div class="text-4xl mb-3">🧠</div>
                    <h3 class="text-lg font-bold text-sky-700">LPSK (Perlindungan Saksi)</h3>
                    <p class="text-sm text-sky-600 mt-1">Pendampingan trauma</p>
                    <a href="tel:02129414555" class="inline-block mt-3 bg-sky-500 hover:bg-sky-600 text-white px-6 py-3 rounded-full font-bold text-lg transition shadow-md">(021) 2941-4555</a>
                </div>

                <div class="bg-emerald-50 rounded-3xl p-6 border border-emerald-200 shadow-sm">
                    <div class="text-4xl mb-3">🌿</div>
                    <h3 class="text-lg font-bold text-emerald-700">Teknik Pernapasan</h3>
                    <p class="text-sm text-emerald-600 mt-1">Tenangkan dirimu sekarang</p>
                    <a href="{{ route('sos.breathing') }}" class="inline-block mt-3 bg-emerald-500 hover:bg-emerald-600 text-white px-6 py-3 rounded-full font-bold text-lg transition shadow-md">Mulai Breathing Exercise</a>
                </div>

                <div class="bg-indigo-50 rounded-3xl p-6 border border-indigo-200 shadow-sm">
                    <div class="text-4xl mb-3">👁️</div>
                    <h3 class="text-lg font-bold text-indigo-700">Grounding 5-4-3-2-1</h3>
                    <p class="text-sm text-indigo-600 mt-1">Teknik kesadaran penuh saat cemas</p>
                    <a href="{{ route('sos.grounding') }}" class="inline-block mt-3 bg-indigo-500 hover:bg-indigo-600 text-white px-6 py-3 rounded-full font-bold text-lg transition shadow-md">Mulai Grounding</a>
                </div>
            </div>

            <div class="bg-white rounded-3xl p-8 shadow-sm border border-[#E5E7EB] text-center">
                <div class="text-5xl mb-4">💬</div>
                <h2 class="text-xl font-bold text-[#374151] mb-2">Butuh Bicara dengan Hexa AI?</h2>
                <p class="text-[#6B7280] mb-6">Hexa AI siap mendengarkan keluh kesahmu kapan pun.</p>
                <a href="{{ route('services.index') }}" class="inline-block bg-[#C084FC] hover:bg-[#7E22CE] text-white px-8 py-3 rounded-full font-medium transition shadow-md">Mulai Konseling</a>
            </div>
        </div>
    </div>
</x-app-layout>
