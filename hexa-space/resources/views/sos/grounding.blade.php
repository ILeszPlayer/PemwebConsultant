<x-app-layout>
    <div class="py-12 bg-[#F8F3FF] min-h-screen">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <a href="{{ route('sos.index') }}" class="text-[#C084FC] hover:text-[#7E22CE] text-sm font-medium transition">&larr; Kembali ke SOS</a>
            </div>

            <div class="bg-white rounded-3xl p-8 shadow-sm border border-[#E5E7EB] mb-8">
                <div class="text-center mb-8">
                    <h1 class="text-3xl font-bold text-[#374151]">👁️ Teknik Grounding 5-4-3-2-1</h1>
                    <p class="text-[#6B7280] mt-2">Teknik ini membantu menenangkan saat kamu merasa cemas, panik, atau terputus dari realitas.</p>
                </div>

                <div id="stepContainer" class="space-y-6">
                    <div id="step1" class="p-6 bg-rose-50 rounded-2xl border border-rose-200 text-center step-card">
                        <div class="text-4xl mb-3">👀</div>
                        <h3 class="text-xl font-bold text-rose-700 mb-2">5 Hal yang Bisa Kamu Lihat</h3>
                        <p class="text-rose-600 mb-4">Lihat sekelilingmu dan sebutkan 5 benda yang bisa kamu lihat.</p>
                        <div class="grid grid-cols-5 gap-2 max-w-md mx-auto">
                            <input type="text" placeholder="1" class="grounding-input text-center border border-rose-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-rose-400 bg-white">
                            <input type="text" placeholder="2" class="grounding-input text-center border border-rose-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-rose-400 bg-white">
                            <input type="text" placeholder="3" class="grounding-input text-center border border-rose-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-rose-400 bg-white">
                            <input type="text" placeholder="4" class="grounding-input text-center border border-rose-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-rose-400 bg-white">
                            <input type="text" placeholder="5" class="grounding-input text-center border border-rose-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-rose-400 bg-white">
                        </div>
                    </div>

                    <div id="step2" class="p-6 bg-orange-50 rounded-2xl border border-orange-200 text-center step-card">
                        <div class="text-4xl mb-3">🤲</div>
                        <h3 class="text-xl font-bold text-orange-700 mb-2">4 Hal yang Bisa Kamu Sentuh</h3>
                        <p class="text-orange-600 mb-4">Rasakan dan sebutkan 4 benda yang bisa kamu sentuh.</p>
                        <div class="grid grid-cols-4 gap-2 max-w-sm mx-auto">
                            <input type="text" placeholder="1" class="grounding-input text-center border border-orange-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 bg-white">
                            <input type="text" placeholder="2" class="grounding-input text-center border border-orange-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 bg-white">
                            <input type="text" placeholder="3" class="grounding-input text-center border border-orange-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 bg-white">
                            <input type="text" placeholder="4" class="grounding-input text-center border border-orange-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 bg-white">
                        </div>
                    </div>

                    <div id="step3" class="p-6 bg-yellow-50 rounded-2xl border border-yellow-200 text-center step-card">
                        <div class="text-4xl mb-3">👂</div>
                        <h3 class="text-xl font-bold text-yellow-700 mb-2">3 Hal yang Bisa Kamu Dengar</h3>
                        <p class="text-yellow-600 mb-4">Dengarkan dan sebutkan 3 suara yang kamu dengar saat ini.</p>
                        <div class="grid grid-cols-3 gap-2 max-w-xs mx-auto">
                            <input type="text" placeholder="1" class="grounding-input text-center border border-yellow-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400 bg-white">
                            <input type="text" placeholder="2" class="grounding-input text-center border border-yellow-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400 bg-white">
                            <input type="text" placeholder="3" class="grounding-input text-center border border-yellow-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400 bg-white">
                        </div>
                    </div>

                    <div id="step4" class="p-6 bg-green-50 rounded-2xl border border-green-200 text-center step-card">
                        <div class="text-4xl mb-3">👃</div>
                        <h3 class="text-xl font-bold text-green-700 mb-2">2 Hal yang Bisa Kamu Cium</h3>
                        <p class="text-green-600 mb-4">Cium dan sebutkan 2 aroma yang tercium di sekitarmu.</p>
                        <div class="grid grid-cols-2 gap-2 max-w-xs mx-auto">
                            <input type="text" placeholder="1" class="grounding-input text-center border border-green-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400 bg-white">
                            <input type="text" placeholder="2" class="grounding-input text-center border border-green-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400 bg-white">
                        </div>
                    </div>

                    <div id="step5" class="p-6 bg-blue-50 rounded-2xl border border-blue-200 text-center step-card">
                        <div class="text-4xl mb-3">👅</div>
                        <h3 class="text-xl font-bold text-blue-700 mb-2">1 Hal yang Bisa Kamu Rasakan</h3>
                        <p class="text-blue-600 mb-4">Rasakan dan sebutkan 1 rasa yang ada di mulutmu.</p>
                        <div class="max-w-xs mx-auto">
                            <input type="text" placeholder="..." class="grounding-input text-center border border-blue-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 bg-white w-full">
                        </div>
                    </div>
                </div>

                <div class="mt-8 text-center">
                    <p class="text-sm text-[#6B7280] mb-4">Setelah selesai, tarik napas dalam-dalam dan rasakan perbedaannya.</p>
                    <div class="flex gap-4 justify-center">
                        <a href="{{ route('sos.breathing') }}" class="bg-emerald-400 hover:bg-emerald-500 text-white px-8 py-3 rounded-full font-medium transition shadow-md">🌬️ Lanjut Breathing</a>
                        <a href="{{ route('services.index') }}" class="bg-[#C084FC] hover:bg-[#7E22CE] text-white px-8 py-3 rounded-full font-medium transition shadow-md">💬 Cerita ke Kakak</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
