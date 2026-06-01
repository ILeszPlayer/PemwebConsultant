<x-app-layout>
    <div class="min-h-screen bg-[#F8F3FF] py-8 px-4">
        <div class="max-w-4xl mx-auto bg-white rounded-3xl shadow-xl border border-purple-100 overflow-hidden flex flex-col h-[85vh]">

            <div class="p-6 bg-gradient-to-r from-[#E9D5FF] via-[#FCE7F3] to-[#F8F3FF] border-b border-purple-100 flex justify-between items-center">
                <div class="flex items-center space-x-4">
                    <div class="w-3 h-3 rounded-full bg-emerald-400 animate-pulse"></div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-xl font-bold text-gray-800">{{ $session->title }}</h2>
                            @if($session->status !== 'finished')
                                <button onclick="editTitle()" class="text-gray-400 hover:text-[#7E22CE] transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                </button>
                            @endif
                        </div>
                        <p class="text-xs text-gray-500 font-medium">Layanan: {{ $session->counselingService->name ?? '-' }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('sessions.export', $session) }}" class="px-3 py-2 bg-white/80 hover:bg-white text-gray-600 font-semibold text-xs rounded-full transition-all shadow-sm border border-purple-100" title="Ekspor Chat">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                        Export
                    </a>
                    @if($session->status !== 'finished')
                        <button onclick="openMoodModal()" class="px-4 py-2 bg-rose-400 hover:bg-rose-500 text-white font-semibold text-xs rounded-full transition-all shadow-md">
                            Akhiri Sesi Cerita
                        </button>
                    @endif
                </div>
            </div>

            <div class="flex-1 overflow-y-auto p-6 space-y-4 bg-[#F8F3FF]/40 scroll-smooth" id="chatContainer">
                @forelse($messages as $msg)
                    @if($msg->sender === 'user')
                        <div class="flex justify-end animate-fadeIn group">
                            <div class="max-w-[75%] bg-[#C084FC] text-white px-5 py-3 rounded-2xl rounded-tr-none shadow-sm text-sm font-medium leading-relaxed msg-user-text relative">
                                <div class="pr-4">{{ $msg->message }}</div>
                                <div class="text-[10px] text-purple-200 text-right mt-1">{{ $msg->created_at->format('H:i') }}</div>
                                @if($session->status !== 'finished')
                                    <form action="{{ route('chat-messages.destroy', $msg) }}" method="POST" class="absolute -top-2 -right-2 hidden group-hover:block">
                                        @csrf @method('DELETE')
                                        <button class="bg-red-400 hover:bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs shadow" onclick="return confirm('Hapus pesan ini?')">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="flex justify-start animate-fadeIn">
                            <div class="max-w-[75%] bg-white border border-purple-100 text-gray-800 px-5 py-3 rounded-2xl rounded-tl-none shadow-sm text-sm leading-relaxed flex flex-col">
                                <span class="font-normal text-gray-700 msg-ai-text">{!! nl2br(e($msg->message)) !!}</span>
                                <div class="flex items-center justify-between pt-2 mt-2 border-t border-purple-100/60">
                                    <span class="text-[10px] text-slate-400 font-normal tracking-wide">
                                        Hexa AI &middot; {{ $msg->created_at->format('H:i') }}
                                    </span>
                                    @if($session->status !== 'finished')
                                        <div class="flex gap-1">
                                            <form action="{{ route('chat-messages.feedback', $msg) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="feedback" value="like">
                                                <button class="text-xs text-gray-400 hover:text-green-500 transition p-1" title="Bermanfaat">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5" /></svg>
                                                </button>
                                            </form>
                                            <form action="{{ route('chat-messages.feedback', $msg) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="feedback" value="dislike">
                                                <button class="text-xs text-gray-400 hover:text-red-500 transition p-1" title="Kurang membantu">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14H5.236a2 2 0 01-1.789-2.894l3.5-7A2 2 0 018.736 3h4.018a2 2 0 01.485.06l3.76.94m-7 10v5a2 2 0 002 2h.096c.5 0 .905-.405.905-.904 0-.715.211-1.413.608-2.008L17 13V4m-7 10h2m5-10h2a2 2 0 012 2v6a2 2 0 01-2 2h-2.5" /></svg>
                                                </button>
                                            </form>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                @empty
                    <div class="text-center py-12 text-gray-400 text-sm italic">
                        Belum ada obrolan di sini. Ruang ini sepenuhnya aman untukmu, mulailah menuliskan keluhan atau ceritamu secara bebas...
                    </div>
                @endforelse

                {{-- Typing Indicator --}}
                <div id="typingIndicator" class="flex justify-start hidden animate-fadeIn">
                    <div class="bg-white border border-purple-100 text-gray-800 px-5 py-3 rounded-2xl rounded-tl-none shadow-sm">
                        <div class="flex gap-1">
                            <span class="w-2 h-2 bg-[#C084FC] rounded-full animate-bounce" style="animation-delay: 0s"></span>
                            <span class="w-2 h-2 bg-[#C084FC] rounded-full animate-bounce" style="animation-delay: 0.15s"></span>
                            <span class="w-2 h-2 bg-[#C084FC] rounded-full animate-bounce" style="animation-delay: 0.3s"></span>
                        </div>
                    </div>
                </div>
            </div>

            <div id="predictiveContainer" class="px-6 py-2 bg-white flex flex-wrap gap-2 transition-all duration-300"></div>

            <div class="p-4 bg-white border-t border-purple-50">
                @if($session->status !== 'finished')
                    <form id="chatForm" action="{{ route('sessions.chat', $session->id) }}" method="POST" class="flex flex-col">
                        @csrf
                        <div class="flex items-center space-x-3">
                            <div class="flex-1 relative">
                                <textarea id="messageInput" name="message" rows="1" maxlength="1000" placeholder="Tuliskan apa yang sedang kamu rasakan saat ini... Ruang ini sepenuhnya aman untukmu."
                                    class="w-full border border-purple-200 rounded-2xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#C084FC] bg-[#F8F3FF]/30 text-gray-700 resize-none shadow-inner pr-16"
                                    required></textarea>
                                <span id="charCount" class="absolute bottom-2 right-3 text-[10px] text-gray-400 font-medium">0/1000</span>
                            </div>
                            <button type="submit" id="submitBtn" class="bg-[#C084FC] hover:bg-[#7E22CE] text-white p-3 rounded-xl transition-all shadow-lg flex items-center justify-center shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z" />
                                </svg>
                            </button>
                        </div>
                    </form>
                @else
                    <div class="p-3 bg-purple-50 text-center rounded-2xl text-xs font-semibold text-[#7E22CE] italic">
                        Sesi konseling ini telah selesai diarsipkan secara rahasia dan aman.
                    </div>
                @endif
            </div>

            {{-- Addiction Barrier Widget --}}
            <div id="addictionBarrier" class="fixed inset-0 bg-black/60 hidden items-center justify-center z-50 p-4">
                <div class="bg-white rounded-3xl max-w-lg w-full p-8 text-center shadow-2xl border border-rose-200">
                    <div class="text-6xl mb-4">🚨</div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Kakak Peduli Sama Kamu</h3>
                    <p class="text-sm text-gray-500 mb-6">Kakak mendeteksi kamu mungkin sedang mengalami masalah dengan judi online. Ini serius, tapi kamu tidak sendiri.</p>
                    <div class="space-y-3 text-left mb-6">
                        <div class="flex items-start gap-3 p-3 bg-rose-50 rounded-2xl">
                            <span class="text-xl shrink-0">📞</span>
                            <div>
                                <p class="font-semibold text-gray-800 text-sm">Hotline Kemenkes</p>
                                <p class="text-xs text-gray-500">119 (Ekstensi 8) — 24 jam</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3 p-3 bg-purple-50 rounded-2xl">
                            <span class="text-xl shrink-0">💜</span>
                            <div>
                                <p class="font-semibold text-gray-800 text-sm">Yayasan Pulih</p>
                                <p class="text-xs text-gray-500">(021) 7884-2599 — Pendampingan psikososial</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3 p-3 bg-sky-50 rounded-2xl">
                            <span class="text-xl shrink-0">🧠</span>
                            <div>
                                <p class="font-semibold text-gray-800 text-sm">LPSK</p>
                                <p class="text-xs text-gray-500">(021) 2941-4555 — Perlindungan Saksi & Korban</p>
                            </div>
                        </div>
                    </div>
                    <button onclick="closeAddictionBarrier()" class="w-full bg-[#C084FC] hover:bg-[#7E22CE] text-white px-6 py-3 rounded-full font-medium transition shadow-md">Baik, Kakak, terima kasih</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Mood Modal --}}
    <div id="moodModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">
        <div class="bg-white rounded-3xl max-w-md w-full p-6 text-center shadow-2xl border border-purple-100">
            <h3 class="text-lg font-bold text-gray-800 mb-2">Gimana Perasaanmu Sekarang?</h3>
            <p class="text-xs text-gray-500 mb-6">Pilihanmu mendikte kebutuhan tindakan eskalasi lanjutan.</p>

            <form action="{{ route('sessions.finish', $session->id) }}" method="POST" class="space-y-3">
                @csrf
                @method('PATCH')
                <button type="submit" name="final_mood" value="better" class="w-full py-3 px-4 border border-purple-100 hover:border-[#C084FC] bg-[#F8F3FF]/50 rounded-2xl text-sm flex justify-between items-center font-semibold text-gray-700 transition-all">
                    <span>😊 Jauh Lebih Tenang &amp; Lega</span>
                    <span class="text-[10px] bg-purple-100 px-2 py-0.5 rounded text-purple-700 font-bold">Arsip Selesai</span>
                </button>
                <button type="submit" name="final_mood" value="need_doctor" class="w-full py-3 px-4 border border-rose-200 hover:border-rose-400 bg-rose-50/40 rounded-2xl text-sm flex justify-between items-center text-rose-700 font-extrabold transition-all">
                    <span>🙁 Masih Butuh Bantuan Ahli (Doktor)</span>
                    <span class="text-[10px] bg-rose-500 px-2 py-0.5 rounded text-white font-bold">Eskalasi Medis</span>
                </button>
            </form>
        </div>
    </div>

    {{-- Title Edit Modal --}}
    <div id="titleModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">
        <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-purple-100">
            <h3 class="text-lg font-bold text-gray-800 mb-2">Edit Judul Sesi</h3>
            <form action="{{ route('sessions.updateTitle', $session->id) }}" method="POST" class="mt-4">
                @csrf @method('PATCH')
                <input type="text" name="title" value="{{ $session->title }}" class="w-full border border-[#E5E7EB] rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#C084FC] mb-4" required>
                <div class="flex gap-3">
                    <button type="button" onclick="closeTitleModal()" class="flex-1 px-4 py-2 border border-[#E5E7EB] rounded-xl text-sm font-medium text-[#6B7280] hover:bg-gray-50 transition">Batal</button>
                    <button type="submit" class="flex-1 bg-[#C084FC] hover:bg-[#7E22CE] text-white px-4 py-2 rounded-xl text-sm font-medium transition">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <style>
        .animate-fadeIn { animation: fadeIn 0.3s ease-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }
    </style>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const chatContainer = document.getElementById('chatContainer');
        const chatForm = document.getElementById('chatForm');
        const submitBtn = document.getElementById('submitBtn');
        const messageInput = document.getElementById('messageInput');
        const predictiveContainer = document.getElementById('predictiveContainer');
        const typingIndicator = document.getElementById('typingIndicator');
        const charCount = document.getElementById('charCount');

        function scrollToBottom() {
            if (chatContainer) {
                chatContainer.scrollTop = chatContainer.scrollHeight;
            }
        }
        scrollToBottom();

        // Character counter
        if (messageInput && charCount) {
            messageInput.addEventListener('input', function () {
                const len = this.value.length;
                charCount.textContent = len + '/1000';
                charCount.style.color = len > 900 ? '#ef4444' : len > 750 ? '#f59e0b' : '#9ca3af';
            });
        }

        if (chatForm) {
            chatForm.addEventListener('submit', function () {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
                typingIndicator.classList.remove('hidden');
                scrollToBottom();
            });
        }

        // === CONTEXTUAL PREDICTIVE CHIPS ===
        // === CONTEXTUAL SUGGESTIONS ===
        const chatHistoryText = Array.from(document.querySelectorAll('.msg-user-text, .msg-ai-text'))
            .map(el => el.textContent.toLowerCase())
            .join(' ');

        // key → keywords to detect if topic is already discussed
        const topicKeywords = {
            judol:    ['judi', 'judol', 'slot', 'gacor', 'depo', 'togel', 'kecanduan', 'adiksi', 'rugi', 'tabungan'],
            sekolah:  ['ukt', 'kuliah', 'sekolah', 'tugas', 'semester', 'kampus', 'nilai', 'univ', 'dosen'],
            hubungan: ['pacar', 'mantan', 'cinta', 'selingkuh', 'putus', 'sayang', 'pasangan'],
            cemas:    ['cemas', 'takut', 'panik', 'gelisah', 'khawatir', 'stress', 'deg-degan'],
            sedih:    ['sedih', 'nangis', 'sendiri', 'kesepian', 'hampa', 'putus asa'],
            keluarga: ['ayah', 'ibu', 'ortu', 'keluarga', 'adik', 'kakak'],
            teman:    ['teman', 'sahabat', 'pertemanan'],
            trauma:   ['trauma', 'masa lalu', 'luka', 'kenangan'],
            pusing:   ['pusing', 'beban', 'berat', 'bingung', 'capek'],
        };

        const discussedTopics = new Set();
        for (const [topic, kws] of Object.entries(topicKeywords)) {
            if (kws.some(kw => chatHistoryText.includes(kw))) {
                discussedTopics.add(topic);
            }
        }

        const masterPhrasesDataset = {
            judol: [
                "aku kecanduan judi online sampai tabunganku habis",
                "gimana cara berhenti judi online?",
                "aku udah rugi banyak karena judi",
                "aku deposit top up terus sampai 3 juta lebih",
                "aku kecanduan top up judi online",
            ],
            sekolah: [
                "uang kuliahku habis dipakai judi online",
                "gimana bilang ke orang tua kalau uang UKT habis?",
                "aku kecanduan judi dan takut gak bisa bayar UKT",
                "aku takut gak lulus karena masalah keuangan",
            ],
            hubungan: [
                "hubunganku sama pacar mulai renggang",
                "aku takut pasanganku selingkuh",
                "gimana cara ngomong serius sama pasangan?",
                "aku merasa dikhianati",
                "gimana cara memperbaiki hubungan yang retak?",
            ],
            cemas: [
                "aku merasa cemas terus menerus",
                "aku takut menghadapi masa depan",
                "jantungku berdegup kencang karena takut",
                "aku overthinking terus",
            ],
            sedih: [
                "aku merasa sedih tanpa alasan jelas",
                "aku kehilangan semangat buat apapun",
                "aku merasa sendirian",
                "aku sering nangis sendiri",
            ],
            keluarga: [
                "aku punya masalah dengan orang tuaku",
                "orang tuaku gak ngerti perasaanku",
                "aku takut bilang ke keluarga",
                "aku malu sama orang tua",
            ],
            teman: [
                "aku punya masalah dengan temanku",
                "aku dijauhin teman-temanku",
                "aku gak punya teman cerita",
                "aku dikhianati teman dekat",
            ],
            trauma: [
                "aku punya trauma dari masa lalu",
                "kenangan buruk terus menghantuiku",
                "aku merasa terganggu dengan masa laluku",
                "aku susah move on dari kejadian masa lalu",
            ],
            pusing: [
                "pusing mikirin masalah hidup",
                "beban hidup rasanya berat banget",
                "aku bingung harus gimana",
                "aku stres mikirin semuanya sekaligus",
            ],
        };

        if (messageInput) {
            messageInput.addEventListener('input', function () {
                const query = this.value.toLowerCase().trim();
                predictiveContainer.innerHTML = '';
                if (query.length < 2) return;

                let matches = [];

                for (const [topic, phrases] of Object.entries(masterPhrasesDataset)) {
                    // Only show chips from topics NOT YET discussed
                    if (discussedTopics.has(topic)) continue;

                    for (const phrase of phrases) {
                        if (phrase.toLowerCase().includes(query)) {
                            matches.push(phrase);
                        }
                    }
                }

                // If no matches from undiscussed topics, also show from any topic
                if (matches.length === 0) {
                    for (const phrases of Object.values(masterPhrasesDataset)) {
                        for (const phrase of phrases) {
                            if (phrase.toLowerCase().includes(query)) {
                                matches.push(phrase);
                            }
                        }
                    }
                }

                const uniqueChips = [...new Set(matches)];
                uniqueChips.slice(0, 3).forEach(phrase => {
                    const chip = document.createElement('button');
                    chip.type = 'button';
                    chip.className = 'px-3 py-1.5 bg-[#F8F3FF] hover:bg-[#E9D5FF] text-[#7E22CE] border border-purple-100 rounded-full text-xs font-semibold shadow-sm transition-all transform hover:scale-105 animate-fadeIn duration-150';
                    chip.innerText = phrase;
                    chip.addEventListener('click', function () {
                        messageInput.value = phrase;
                        const event = new Event('input');
                        messageInput.dispatchEvent(event);
                        predictiveContainer.innerHTML = '';
                        messageInput.focus();
                    });
                    predictiveContainer.appendChild(chip);
                });
            });
        }

        // Auto-resize textarea
        if (messageInput) {
            messageInput.addEventListener('input', function () {
                this.style.height = 'auto';
                this.style.height = Math.min(this.scrollHeight, 120) + 'px';
            });
        }

        // Detect addiction keywords from chat history and show barrier
        function checkAddictionContext() {
            const barrier = document.getElementById('addictionBarrier');
            if (!barrier) return;

            const allText = Array.from(document.querySelectorAll('.msg-user-text'))
                .map(el => el.textContent.toLowerCase())
                .join(' ');

            const addictionWords = ['judi', 'judol', 'slot', 'gacor', 'depo', 'togel', 'casino', 'kecanduan', 'adiksi'];
            const hasAddictionContext = addictionWords.some(word => allText.includes(word));

            if (hasAddictionContext) {
                barrier.classList.remove('hidden');
                barrier.classList.add('flex');
            }
        }
        checkAddictionContext();

        // Detect addiction keywords while typing
        if (messageInput) {
            messageInput.addEventListener('input', function () {
                const val = this.value.toLowerCase();
                const barrier = document.getElementById('addictionBarrier');
                if (barrier && (val.includes('judi') || val.includes('judol') || val.includes('slot') || val.includes('gacor') || val.includes('depo') || val.includes('togel'))) {
                    barrier.classList.remove('hidden');
                    barrier.classList.add('flex');
                }
            });
        }
    });

    function closeAddictionBarrier() {
        const el = document.getElementById('addictionBarrier');
        el.classList.add('hidden');
        el.classList.remove('flex');
    }

    function openMoodModal() {
        document.getElementById('moodModal').classList.remove('hidden');
        document.getElementById('moodModal').classList.add('flex');
    }

    function editTitle() {
        document.getElementById('titleModal').classList.remove('hidden');
        document.getElementById('titleModal').classList.add('flex');
    }

    function closeTitleModal() {
        document.getElementById('titleModal').classList.add('hidden');
        document.getElementById('titleModal').classList.remove('flex');
    }
    </script>
</x-app-layout>
