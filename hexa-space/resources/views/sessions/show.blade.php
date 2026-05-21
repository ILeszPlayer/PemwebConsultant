<x-app-layout>
    <div class="min-h-screen bg-[#F8F3FF] py-8 px-4">
        <div class="max-w-4xl mx-auto bg-white rounded-3xl shadow-xl border border-purple-100 overflow-hidden flex flex-col h-[85vh]">
            
            <div class="p-6 bg-gradient-to-r from-[#E9D5FF] via-[#FCE7F3] to-[#F8F3FF] border-b border-purple-100 flex justify-between items-center">
                <div class="flex items-center space-x-4">
                    <div class="w-3 h-3 rounded-full bg-emerald-400 animate-pulse"></div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-800">Ruang Aman Hexa AI</h2>
                        <p class="text-xs text-gray-500 font-medium">Layanan: {{ $session->title }}</p>
                    </div>
                </div>
                @if($session->status !== 'finished')
                    <button onclick="openMoodModal()" class="px-4 py-2 bg-rose-400 hover:bg-rose-500 text-white font-semibold text-xs rounded-full transition-all shadow-md">
                        Akhiri Sesi Cerita
                    </button>
                @endif
            </div>

            <div class="flex-1 overflow-y-auto p-6 space-y-4 bg-[#F8F3FF]/40" id="chatContainer">
                @forelse($messages as $msg)
                    @if($msg->sender === 'user')
                        <div class="flex justify-end animate-fadeIn">
                            <div class="max-w-[75%] bg-[#C084FC] text-white px-5 py-3 rounded-2xl rounded-tr-none shadow-sm text-sm font-medium leading-relaxed msg-user-text">
                                {{ $msg->message }}
                            </div>
                        </div>
                    @else
                        <div class="flex justify-start animate-fadeIn">
                            <div class="max-w-[75%] bg-white border border-purple-100 text-gray-800 px-5 py-3 rounded-2xl rounded-tl-none shadow-sm text-sm leading-relaxed flex flex-col">
                                <span class="font-normal text-gray-700 msg-ai-text">{{ $msg->message }}</span>
                                <div class="border-t border-purple-100/60 pt-2 mt-2">
                                    <span class="text-[10px] text-slate-400 italic font-normal tracking-wide">
                                        ⚠️ Pesan otomatis sistem Hexa Space. Bukan merupakan diagnosis profesional psikologis. Gejala yang muncul akibat tekanan situasi yang sedang kamu alami.
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endif
                @empty
                    <div class="text-center py-12 text-gray-400 text-sm italic">
                        Belum ada obrolan di sini. Ruang ini sepenuhnya aman untukmu, mulailah menuliskan ceritamu...
                    </div>
                @endforelse
            </div>

            <div id="predictiveContainer" class="px-6 py-2 bg-white flex flex-wrap gap-2 transition-all duration-300"></div>

            <div class="p-4 bg-white border-t border-purple-50">
                @if($session->status !== 'finished')
                    <form id="chatForm" action="{{ route('sessions.chat', $session->id) }}" method="POST" class="flex items-center space-x-3">
                        @csrf
                        <textarea 
                            id="messageInput" 
                            name="message" 
                            rows="1" 
                            placeholder="Tuliskan apa yang sedang kamu rasakan saat ini... Ruang ini sepenuhnya aman untukmu."
                            class="flex-1 border border-purple-200 rounded-2xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#C084FC] bg-[#F8F3FF]/30 text-gray-700 resize-none w-full shadow-inner"
                            required></textarea>
                        
                        <button type="submit" id="submitBtn" class="bg-[#C084FC] hover:bg-[#7E22CE] text-white p-3 rounded-xl transition-all shadow-lg flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z" />
                            </svg>
                        </button>
                    </form>
                @else
                    <div class="p-3 bg-purple-50 text-center rounded-2xl text-xs font-semibold text-[#7E22CE] italic">
                        Sesi konseling ini telah selesai diarsipkan secara rahasia dan aman.
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div id="moodModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">
        <div class="bg-white rounded-3xl max-w-md w-full p-6 text-center shadow-2xl border border-purple-100">
            <h3 class="text-lg font-bold text-gray-800 mb-2">Gimana Perasaanmu Sekarang?</h3>
            <p class="text-xs text-gray-500 mb-6">Pilihanmu membantu kami mendeteksi tingkat keparahan krisis mental untuk penanganan selanjutnya.</p>
            
            <form action="{{ route('sessions.finish', $session->id) }}" method="POST" class="space-y-3">
                @csrf
                @method('PATCH')
                <button type="submit" name="final_mood" value="better" class="w-full py-3 px-4 border border-purple-100 hover:border-[#C084FC] bg-[#F8F3FF]/50 rounded-2xl text-sm flex justify-between items-center font-semibold text-gray-700 transition-all">
                    <span>😊 Jauh Lebih Tenang & Lega</span>
                    <span class="text-[10px] bg-purple-100 px-2 py-0.5 rounded text-purple-700 font-bold">Arsip Mandiri</span>
                </button>
                <button type="submit" name="final_mood" value="need_doctor" class="w-full py-3 px-4 border border-rose-200 hover:border-rose-400 bg-rose-50/40 rounded-2xl text-sm flex justify-between items-center text-rose-700 font-extrabold transition-all">
                    <span>🙁 Masih Butuh Bantuan Ahli (Doktor)</span>
                    <span class="text-[10px] bg-rose-500 px-2 py-0.5 rounded text-white font-bold">Eskalasi Medis 🔥</span>
                </button>
            </form>
        </div>
    </div>

    <style>
        .animate-fadeIn {
            animation: fadeIn 0.3s ease-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const chatContainer = document.getElementById('chatContainer');
        if (chatContainer) chatContainer.scrollTop = chatContainer.scrollHeight;

        const chatForm = document.getElementById('chatForm');
        const submitBtn = document.getElementById('submitBtn');
        const messageInput = document.getElementById('messageInput');
        const predictiveContainer = document.getElementById('predictiveContainer');

        if (chatForm) {
            chatForm.addEventListener('submit', function () {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
            });
        }

        const contextScreenText = Array.from(document.querySelectorAll('.msg-user-text, .msg-ai-text'))
            .map(el => el.innerText.toLowerCase())
            .join(' ');

        const masterPhrasesDataset = {
            'pus': ["pusing memikirkan jalan keluar masalah ini", "pusing karena uang ukt habis terpakai judol", "kepalaku rasanya pusing sekali mau pecah menghadapinya"],
            'str': ["stres berat sampai tidak bisa tidur nyenyak berhari-hari", "stres memikirkan kebohongan besar yang kuperbuat"],
            'cem': ["cemas berlebihan dan jantungku berdegup kencang ketakutan", "cemas memikirkan masa depanku yang berantakan"],
            'jud': ["bagaimana cara konkret keluar dari lingkaran adiksi judi online?", "aku kecanduan judi online sampai tabunganku ludes habis"],
            'ukt': ["uang ukt kuliahku habis total dipakai deposit link gacor", "takut drop out dari kampus gara-gara uang ukt ludes", "bagaimana cara berterus terang kalau uang kuliah terpakai?"],
            'ibu': ["gimana cara aku ngomong ke ibu tentang ini?", "tolong sarankan semua langkah agar aku bisa pulih", "aku takut sekali mengecewakan ibu jika berkata sejujurnya"],
            'mam': ["bagaimana cara aku berterus terang ke mama tentang judi ini?", "aku takut dimarahi mama karena menghilangkan uang kuliah"],
            'tak': ["aku takut dimarahi orang tua karena berbuat salah", "aku takut jujur ke kakak soal uang UKT habis", "takut dihakimi dan dikucilkan oleh keluarga sendiri"],
            'ove': ["pikiranku sangat berisik dan selalu overthinking setiap malam"]
        };

        if (messageInput) {
            messageInput.addEventListener('input', function () {
                const query = this.value.toLowerCase().trim();
                predictiveContainer.innerHTML = '';

                if (query.length < 2) return;

                let matches = [];

                if (contextScreenText.includes('judi') || contextScreenText.includes('gacor') || contextScreenText.includes('3 juta') || contextScreenText.includes('ukt')) {
                    if ('ibu'.startsWith(query) || query.includes('ib') || query.includes('ngomong')) {
                        matches = matches.concat(masterPhrasesDataset['ibu']);
                    }
                    if ('takut'.startsWith(query) || query.includes('ta') || query.includes('marah')) {
                        matches = matches.concat(masterPhrasesDataset['tak']);
                    }
                    if ('ukt'.startsWith(query) || query.includes('uk')) {
                        matches = matches.concat(masterPhrasesDataset['ukt']);
                    }
                }

                for (let key in masterPhrasesDataset) {
                    if (key.startsWith(query) || query.includes(key)) {
                        matches = matches.concat(masterPhrasesDataset[key]);
                    }
                }

                const uniqueChips = [...new Set(matches)];

                uniqueChips.slice(0, 5).forEach(phrase => {
                    const chip = document.createElement('button');
                    chip.type = 'button';
                    chip.className = 'px-3 py-1.5 bg-[#F8F3FF] hover:bg-[#E9D5FF] text-[#7E22CE] border border-purple-100 rounded-full text-xs font-semibold shadow-sm transition-all transform hover:scale-105 animate-fadeIn duration-150';
                    chip.innerText = phrase;

                    chip.addEventListener('click', function () {
                        messageInput.value = phrase;
                        predictiveContainer.innerHTML = '';
                        messageInput.focus();
                    });

                    predictiveContainer.appendChild(chip);
                });
            });
        }
    });

    function openMoodModal() {
        document.getElementById('moodModal').classList.remove('hidden');
        document.getElementById('moodModal').classList.add('flex');
    }
    </script>
</x-app-layout>
