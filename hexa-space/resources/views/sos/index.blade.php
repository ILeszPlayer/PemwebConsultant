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

                <div class="bg-teal-50 rounded-3xl p-6 border border-teal-200 shadow-sm md:col-span-2">
                    <div class="flex items-center gap-4">
                        <div class="text-4xl">🧘</div>
                        <div class="flex-1">
                            <h3 class="text-lg font-bold text-teal-700">Meditasi Terpandu</h3>
                            <p class="text-sm text-teal-600">Dengarkan audio meditasi untuk menenangkan pikiran (5-10 menit)</p>
                        </div>
                        <button onclick="startMeditation()" id="meditationBtn" class="shrink-0 bg-teal-500 hover:bg-teal-600 text-white px-6 py-3 rounded-full font-bold text-lg transition shadow-md">
                            ▶ Mulai Meditasi
                        </button>
                    </div>
                    {{-- Meditation Timer --}}
                    <div id="meditationTimer" class="hidden mt-6 text-center">
                        <div class="text-5xl font-bold text-teal-700 mb-2" id="meditationCountdown">05:00</div>
                        <div class="w-full bg-white/60 rounded-full h-3 overflow-hidden">
                            <div id="meditationProgress" class="bg-teal-500 h-full rounded-full transition-all duration-1000" style="width: 0%"></div>
                        </div>
                        <p class="text-sm text-teal-600 mt-3">Tarik napas dalam-dalam... hembuskan perlahan...</p>
                        <button onclick="stopMeditation()" class="mt-4 text-teal-700 underline text-sm">Hentikan</button>
                    </div>
                </div>
            </div>

            <script>
                let meditationInterval = null;
                let meditationTime = 300; // 5 minutes in seconds
                const totalMeditationTime = 300;

                function startMeditation() {
                    const btn = document.getElementById('meditationBtn');
                    const timer = document.getElementById('meditationTimer');
                    btn.classList.add('hidden');
                    timer.classList.remove('hidden');
                    meditationTime = totalMeditationTime;
                    updateMeditationDisplay();
                    meditationInterval = setInterval(function() {
                        meditationTime--;
                        updateMeditationDisplay();
                        if (meditationTime <= 0) {
                            stopMeditation();
                            alert('🧘 Meditasi selesai! Semoga pikiranmu lebih tenang.');
                        }
                    }, 1000);
                }

                function stopMeditation() {
                    if (meditationInterval) {
                        clearInterval(meditationInterval);
                        meditationInterval = null;
                    }
                    document.getElementById('meditationBtn').classList.remove('hidden');
                    document.getElementById('meditationTimer').classList.add('hidden');
                }

                function updateMeditationDisplay() {
                    const min = String(Math.floor(meditationTime / 60)).padStart(2, '0');
                    const sec = String(meditationTime % 60).padStart(2, '0');
                    document.getElementById('meditationCountdown').textContent = min + ':' + sec;
                    const progress = ((totalMeditationTime - meditationTime) / totalMeditationTime) * 100;
                    document.getElementById('meditationProgress').style.width = progress + '%';
                }
            </script>

            <div class="bg-white rounded-3xl p-8 shadow-sm border border-[#E5E7EB] text-center">
                <div class="text-5xl mb-4">💬</div>
                <h2 class="text-xl font-bold text-[#374151] mb-2">Butuh Bicara dengan Hexa AI?</h2>
                <p class="text-[#6B7280] mb-6">Hexa AI siap mendengarkan keluh kesahmu kapan pun.</p>
                <a href="{{ route('services.index') }}" class="inline-block bg-[#C084FC] hover:bg-[#7E22CE] text-white px-8 py-3 rounded-full font-medium transition shadow-md">Mulai Konseling</a>
            </div>
        </div>
    </div>
</x-app-layout>
