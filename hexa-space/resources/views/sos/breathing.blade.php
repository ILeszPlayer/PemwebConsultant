<x-app-layout>
    <div class="py-12 bg-[#F8F3FF] min-h-screen">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="mb-8">
                <a href="{{ route('sos.index') }}" class="text-[#C084FC] hover:text-[#7E22CE] text-sm font-medium transition">&larr; Kembali</a>
                <h1 class="text-3xl font-bold text-[#374151] mt-2">🌬️ Breathing Exercise</h1>
                <p class="text-[#6B7280] mt-2">Ikuti panduan pernapasan untuk menenangkan diri.</p>
            </div>

            <div class="bg-white rounded-3xl p-12 shadow-sm border border-[#E5E7EB]">
                <div id="breathingCircle" class="w-48 h-48 mx-auto rounded-full bg-gradient-to-r from-[#C084FC] to-[#7E22CE] flex items-center justify-center transition-all duration-1000 shadow-lg cursor-pointer" style="transform: scale(1);">
                    <span id="breathingText" class="text-white text-xl font-bold">Tarik Napas</span>
                </div>

                <div class="mt-8">
                    <p id="instruction" class="text-[#374151] text-lg font-medium">Tarik napas dalam-dalam melalui hidung selama 4 detik...</p>
                    <p id="timer" class="text-4xl font-bold text-[#C084FC] mt-4">4</p>
                </div>

                <div class="mt-8 flex gap-4 justify-center">
                    <button onclick="startBreathing()" class="bg-[#C084FC] hover:bg-[#7E22CE] text-white px-8 py-3 rounded-full font-medium transition shadow-md">Mulai</button>
                    <button onclick="stopBreathing()" class="bg-gray-100 hover:bg-gray-200 text-[#374151] px-8 py-3 rounded-full font-medium transition shadow-sm">Berhenti</button>
                </div>
            </div>

            <div class="mt-8 bg-white rounded-3xl p-6 shadow-sm border border-[#E5E7EB]">
                <h3 class="text-lg font-bold text-[#374151] mb-3">📋 Petunjuk Teknik 4-7-8</h3>
                <div class="grid grid-cols-3 gap-4 text-sm">
                    <div class="p-3 bg-purple-50 rounded-2xl">
                        <span class="block text-2xl font-bold text-purple-600">4</span>
                        <span class="text-[#6B7280]">Detik Tarik Napas</span>
                    </div>
                    <div class="p-3 bg-purple-50 rounded-2xl">
                        <span class="block text-2xl font-bold text-purple-600">7</span>
                        <span class="text-[#6B7280]">Detik Tahan Napas</span>
                    </div>
                    <div class="p-3 bg-purple-50 rounded-2xl">
                        <span class="block text-2xl font-bold text-purple-600">8</span>
                        <span class="text-[#6B7280]">Detik Hembuskan</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let breathingInterval = null;
        let timer = 4;
        let phase = 'inhale';
        let isRunning = false;

        const circle = document.getElementById('breathingCircle');
        const text = document.getElementById('breathingText');
        const instruction = document.getElementById('instruction');
        const timerDisplay = document.getElementById('timer');

        function startBreathing() {
            if (isRunning) return;
            isRunning = true;
            timer = 4;
            phase = 'inhale';
            updatePhase();

            breathingInterval = setInterval(() => {
                timer--;
                timerDisplay.textContent = timer;

                if (timer <= 0) {
                    switch (phase) {
                        case 'inhale':
                            phase = 'hold';
                            timer = 7;
                            break;
                        case 'hold':
                            phase = 'exhale';
                            timer = 8;
                            break;
                        case 'exhale':
                            phase = 'inhale';
                            timer = 4;
                            break;
                    }
                    updatePhase();
                }
            }, 1000);
        }

        function updatePhase() {
            timerDisplay.textContent = timer;

            switch (phase) {
                case 'inhale':
                    text.textContent = 'Tarik Napas';
                    instruction.textContent = 'Tarik napas dalam-dalam melalui hidung...';
                    circle.style.transform = 'scale(1.3)';
                    break;
                case 'hold':
                    text.textContent = 'Tahan Napas';
                    instruction.textContent = 'Tahan napasmu...';
                    circle.style.transform = 'scale(1.3)';
                    break;
                case 'exhale':
                    text.textContent = 'Hembuskan';
                    instruction.textContent = 'Hembuskan perlahan melalui mulut...';
                    circle.style.transform = 'scale(1)';
                    break;
            }
        }

        function stopBreathing() {
            if (breathingInterval) {
                clearInterval(breathingInterval);
                breathingInterval = null;
            }
            isRunning = false;
            timer = 4;
            phase = 'inhale';
            timerDisplay.textContent = '4';
            text.textContent = 'Tarik Napas';
            instruction.textContent = 'Tarik napas dalam-dalam melalui hidung selama 4 detik...';
            circle.style.transform = 'scale(1)';
        }
    </script>
</x-app-layout>
