<x-app-layout>
    <div class="py-6 bg-[#F8F3FF] min-h-screen">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            @if(auth()->user()->role === 'doctor')
                <a href="{{ route('dashboard') }}" class="text-[#6B7280] hover:text-[#374151] text-sm font-medium transition inline-block mb-4">&larr; Kembali ke Dashboard</a>
            @else
                <a href="{{ route('sessions.index') }}" class="text-[#6B7280] hover:text-[#374151] text-sm font-medium transition inline-block mb-4">&larr; Kembali ke Riwayat</a>
            @endif

            <div class="bg-white rounded-2xl p-6 shadow-sm border border-[#E5E7EB] mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-xl font-bold text-[#374151]">{{ $session->title }}</h1>
                        <p class="text-sm text-[#6B7280] mt-1">
                            {{ $session->counselingService->name ?? 'Layanan' }}
                            @if(auth()->user()->role === 'doctor')
                                &middot; Pasien: <span class="font-medium">{{ $session->user->name }}</span>
                            @endif
                        </p>
                    </div>
                    <div class="flex items-center gap-3">
                        <div id="emotionIndicator" class="hidden items-center gap-2 px-4 py-1.5 rounded-full text-xs font-medium transition-all duration-500"></div>
                        @if($session->status === 'active')
                            <span class="px-4 py-1.5 rounded-full text-xs font-medium bg-purple-100 text-purple-700">Sedang Berlangsung</span>
                        @else
                            <span class="px-4 py-1.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500">Selesai</span>
                        @endif
                        @if($session->is_escalated)
                            <span class="px-4 py-1.5 rounded-full text-xs font-medium bg-red-100 text-red-600">Butuh Bantuan Ahli</span>
                        @endif
                    </div>
                </div>
                <div class="mt-3 text-xs text-[#9CA3AF]">
                    Dibuat pada {{ $session->created_at->format('d M Y H:i') }}
                    @if($session->final_mood)
                        &middot; Mood Akhir:
                        @if($session->final_mood === 'lebih_tenang') 😊 Lebih Tenang
                        @elseif($session->final_mood === 'sama_saja') 😐 Sama Saja
                        @elseif($session->final_mood === 'butuh_bantuan') 🙁 Masih Butuh Bantuan
                        @endif
                    @endif
                </div>
            </div>

            @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-700 rounded-2xl px-6 py-4 mb-6">{{ session('error') }}</div>
            @endif

            @if(session('success'))
                <div class="bg-pink-50 border border-pink-200 text-pink-600 rounded-2xl px-6 py-4 mb-6">{{ session('success') }}</div>
            @endif

            @if($session->status === 'finished')
                <div class="bg-purple-50 border border-purple-200 text-purple-700 rounded-2xl px-6 py-5 mb-6 text-center">
                    <div class="text-3xl mb-2">🙏</div>
                    <p class="font-medium">Sesi ini telah berakhir.</p>
                    @if(auth()->user()->role !== 'doctor')
                        <p class="text-sm mt-1">Terima kasih telah berbagi bersama Hexa Space.</p>
                    @endif
                </div>
            @endif

            @if(auth()->user()->role === 'doctor')
                <div class="bg-pink-50 border border-pink-200 text-pink-600 rounded-2xl px-6 py-4 mb-6 text-sm">
                    🔍 Kamu sedang melihat sesi milik <strong>{{ $session->user->name }}</strong> dalam mode baca-saja.
                </div>
            @endif

            {{-- Widget: Box Breathing --}}
            <div id="boxBreathingBanner" class="hidden mb-6 bg-gradient-to-r from-purple-50 to-pink-50 border border-purple-200 rounded-2xl p-6 text-center">
                <div class="flex items-center justify-center gap-4 mb-4">
                    <span class="text-2xl">🌬️</span>
                    <h3 class="text-lg font-bold text-[#7E22CE]">Panduan Pernapasan (Box Breathing)</h3>
                </div>
                <p class="text-sm text-[#6B7280] mb-6" id="breathingInstruction">Tarik napas dalam-dalam...</p>
                <div class="flex items-center justify-center mb-4">
                    <div id="breathingCircle" class="w-24 h-24 rounded-full bg-gradient-to-r from-[#C084FC] to-[#FBCFE8] flex items-center justify-center text-white text-sm font-bold shadow-lg transition-all duration-1000 ease-in-out">
                        <span id="breathingPhase">Tarik</span>
                    </div>
                </div>
                <div class="flex items-center justify-center gap-2 mb-2">
                    <div id="phase1" class="w-3 h-3 rounded-full bg-[#C084FC]"></div>
                    <div id="phase2" class="w-3 h-3 rounded-full bg-[#E9D5FF]"></div>
                    <div id="phase3" class="w-3 h-3 rounded-full bg-[#E9D5FF]"></div>
                    <div id="phase4" class="w-3 h-3 rounded-full bg-[#E9D5FF]"></div>
                </div>
                <p class="text-xs text-[#9CA3AF]">Kamu bisa menutup panduan ini kapan saja</p>
                <button onclick="closeBoxBreathing()" class="mt-3 text-xs text-[#6B7280] hover:text-[#374151] transition font-medium">Tutup Panduan</button>
            </div>

            {{-- Widget: Conflict Roadmap --}}
            <div id="conflictRoadmap" class="hidden mb-6 bg-gradient-to-r from-purple-50 to-pink-50 border border-[#E9D5FF] rounded-2xl p-6 shadow-sm">
                <div class="flex items-center gap-3 mb-5">
                    <span class="text-2xl">🛤️</span>
                    <h3 class="text-lg font-bold text-[#7E22CE]">Panduan Langkah Aksi</h3>
                </div>
                <div class="grid gap-3">
                    <div class="flex items-start gap-4 bg-white rounded-2xl p-4 shadow-sm border border-[#E9D5FF]">
                        <span class="w-8 h-8 rounded-full bg-[#C084FC] text-white text-sm flex items-center justify-center shrink-0 font-bold">1</span>
                        <div>
                            <p class="font-semibold text-[#374151] text-sm">Tenangkan Diri</p>
                            <p class="text-xs text-[#6B7280] mt-0.5">Tarik napas dalam-dalam. Emosi campur aduk itu wajar. Beri waktu dirimu untuk menenangkan diri sebelum bertindak.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4 bg-white rounded-2xl p-4 shadow-sm border border-[#E9D5FF]">
                        <span class="w-8 h-8 rounded-full bg-[#C084FC] text-white text-sm flex items-center justify-center shrink-0 font-bold">2</span>
                        <div>
                            <p class="font-semibold text-[#374151] text-sm">Tulis Kronologi Kejadian</p>
                            <p class="text-xs text-[#6B7280] mt-0.5">Catat secara objektif apa yang kamu lihat, dengar, dan rasakan tanpa menuduh siapa pun. Ini akan membantumu bicara lebih tenang.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4 bg-white rounded-2xl p-4 shadow-sm border border-[#E9D5FF]">
                        <span class="w-8 h-8 rounded-full bg-[#C084FC] text-white text-sm flex items-center justify-center shrink-0 font-bold">3</span>
                        <div>
                            <p class="font-semibold text-[#374151] text-sm">Cari Waktu Senggang Orang Tua</p>
                            <p class="text-xs text-[#6B7280] mt-0.5">Pilih momen tenang seperti setelah makan malam atau akhir pekan. Jangan bicara saat suasana hati sedang panas.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4 bg-white rounded-2xl p-4 shadow-sm border border-[#E9D5FF]">
                        <span class="w-8 h-8 rounded-full bg-[#C084FC] text-white text-sm flex items-center justify-center shrink-0 font-bold">4</span>
                        <div>
                            <p class="font-semibold text-[#374151] text-sm">Bicara Jujur dengan I-Message</p>
                            <p class="text-xs text-[#6B7280] mt-0.5">"Ma/Ayah, aku melihat kejadiannya langsung. Aku belum punya bukti, tapi aku ingin jujur karena tidak mau menyembunyikan apa pun."</p>
                        </div>
                    </div>
                </div>
                <button onclick="closeConflictRoadmap()" class="mt-4 text-xs text-[#6B7280] hover:text-[#374151] transition font-medium block mx-auto">Tutup Panduan</button>
            </div>

            {{-- Widget: Addiction Recovery (De-addiction Roadmap) --}}
            <div id="addictionRecoveryRoadmap" class="hidden mb-6 bg-gradient-to-r from-purple-50 to-pink-50 border border-[#E9D5FF] rounded-2xl p-6 shadow-sm">
                <div class="flex items-center gap-3 mb-5">
                    <span class="text-2xl">🛡️</span>
                    <h3 class="text-lg font-bold text-[#7E22CE]">Panduan Pemulihan Adiksi</h3>
                </div>
                <div class="grid gap-3">
                    <div class="flex items-start gap-4 bg-white rounded-2xl p-4 shadow-sm border border-[#E9D5FF]">
                        <span class="w-8 h-8 rounded-full bg-[#C084FC] text-white text-sm flex items-center justify-center shrink-0 font-bold">1</span>
                        <div>
                            <p class="font-semibold text-[#374151] text-sm">Akui & Terima</p>
                            <p class="text-xs text-[#6B7280] mt-0.5">Akui bahwa ada masalah dengan judi online. Ini bukan berarti kamu lemah — ini adalah langkah pertama menuju pemulihan. Kamu sudah berani mengakuinya dengan bercerita di sini.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4 bg-white rounded-2xl p-4 shadow-sm border border-[#E9D5FF]">
                        <span class="w-8 h-8 rounded-full bg-[#C084FC] text-white text-sm flex items-center justify-center shrink-0 font-bold">2</span>
                        <div>
                            <p class="font-semibold text-[#374151] text-sm">Blokir Akses</p>
                            <p class="text-xs text-[#6B7280] mt-0.5">Blokir situs judi dan hapus aplikasi top-up yang biasa kamu pakai. Minta bantuan teman atau keluarga untuk mengawasi jika perlu. Jauhkan dirimu dari godaan.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4 bg-white rounded-2xl p-4 shadow-sm border border-[#E9D5FF]">
                        <span class="w-8 h-8 rounded-full bg-[#C084FC] text-white text-sm flex items-center justify-center shrink-0 font-bold">3</span>
                        <div>
                            <p class="font-semibold text-[#374151] text-sm">Cari Dukungan</p>
                            <p class="text-xs text-[#6B7280] mt-0.5">Kamu tidak perlu melawan sendiri. Ceritakan ke satu orang yang kamu percaya — teman, keluarga, atau konselor. Jika kesulitan, hubungi Hotline Kemenkes 500-454 untuk bantuan konseling adiksi.</p>
                        </div>
                    </div>
                </div>
                <button onclick="closeAddictionRecovery()" class="mt-4 text-xs text-[#6B7280] hover:text-[#374151] transition font-medium block mx-auto">Tutup Panduan</button>
            </div>

            {{-- Widget: CBT Challenger (Cognitive Reframing) --}}
            <div id="cbtChallengerWidget" class="hidden mb-6 bg-gradient-to-r from-blue-50 to-purple-50 border border-[#C084FC] rounded-2xl p-6 shadow-sm">
                <div class="flex items-center gap-3 mb-5">
                    <span class="text-2xl">🔄</span>
                    <h3 class="text-lg font-bold text-[#7E22CE]">Tantang Pikiran Negatifmu (CBT)</h3>
                </div>
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-[#E9D5FF] mb-4">
                    <p class="text-sm text-[#374151] mb-3" id="cbtNegativeThought">"Aku tidak berguna dan selalu gagal"</p>
                    <div class="border-t border-purple-100 pt-3">
                        <p class="text-xs text-[#6B7280] font-medium mb-2">💡 Tantang pikiran ini:</p>
                        <p class="text-sm text-[#7E22CE] font-semibold" id="cbtChallengeText">"Kamu sedang melalui masa sulit, bukan berarti kamu gagal. Buktinya kamu masih berjuang sampai detik ini."</p>
                    </div>
                </div>
                <div class="grid gap-2">
                    <button onclick="cbtReframe('semua')" class="w-full text-left px-4 py-3 bg-white rounded-2xl border border-[#E9D5FF] hover:bg-purple-50 transition text-sm text-[#374151]">😤 "Aku bodoh dan tidak becus" → <span class="text-[#7E22CE] font-medium">"Aku sedang belajar, dan itu tidak apa-apa."</span></button>
                    <button onclick="cbtReframe('percuma')" class="w-full text-left px-4 py-3 bg-white rounded-2xl border border-[#E9D5FF] hover:bg-purple-50 transition text-sm text-[#374151]">😞 "Semua percuma, aku menyerah" → <span class="text-[#7E22CE] font-medium">"Aku hanya perlu istirahat, bukan menyerah."</span></button>
                    <button onclick="cbtReframe('gagal')" class="w-full text-left px-4 py-3 bg-white rounded-2xl border border-[#E9D5FF] hover:bg-purple-50 transition text-sm text-[#374151]">😢 "Aku selalu gagal dalam hidup" → <span class="text-[#7E22CE] font-medium">"Aku pernah berhasil melewati masa sulit sebelumnya."</span></button>
                </div>
                <button onclick="closeCbtChallenger()" class="mt-4 text-xs text-[#6B7280] hover:text-[#374151] transition font-medium block mx-auto">Tutup Panduan</button>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-[#E5E7EB] p-6 mb-6">
                @if($messages->count() > 0)
                    <div class="space-y-4 max-h-[500px] overflow-y-auto" id="chatMessages">
                        @foreach($messages as $msg)
                            @if($msg->sender === 'user')
                                <div class="flex justify-end">
                                    <div class="max-w-[75%] bg-purple-500 text-white rounded-l-xl rounded-tr-xl px-5 py-3">
                                        <p>{{ $msg->message }}</p>
                                        <p class="text-xs text-purple-200 text-right mt-1">{{ $msg->created_at->format('H:i') }}</p>
                                    </div>
                                </div>
                            @else
                                @php
                                    $msgText = $msg->message;
                                    $disclaimerText = '';
                                    $keyword = 'Bukan merupakan diagnosis profesional psikologis.';
                                    $pos = strpos($msgText, $keyword);
                                    if ($pos !== false) {
                                        $disclaimerText = substr($msgText, $pos);
                                        $msgText = trim(substr($msgText, 0, $pos));
                                    }
                                @endphp
                                <div class="flex justify-start">
                                    <div class="max-w-[75%] bg-white border border-purple-100 text-gray-700 rounded-r-xl rounded-tl-xl px-5 py-3 shadow-sm">
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="text-xs font-semibold text-purple-600">Hexa AI</span>
                                        </div>
                                        <p class="whitespace-pre-line">{{ $msgText }}</p>
                                        @if($disclaimerText)
                                            <div class="border-t border-purple-100/50 pt-1.5 mt-2">
                                                <p class="text-[10px] text-gray-400 italic leading-relaxed">{{ $disclaimerText }}</p>
                                            </div>
                                        @endif
                                        <p class="text-xs text-gray-400 mt-1">{{ $msg->created_at->format('H:i') }}</p>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="text-5xl mb-4">💬</div>
                        <p class="text-[#6B7280] text-lg mb-2">Belum ada pesan di sesi ini.</p>
                        <p class="text-[#9CA3AF] text-sm">Kamu bisa mulai bercerita kapan saja.</p>
                    </div>
                @endif
            </div>

            @if($session->status === 'active' && auth()->user()->role !== 'doctor')
                {{-- Predictive Text Autocomplete Engine --}}
                <div id="predictiveSuggestions" class="mb-3 flex flex-wrap gap-2 min-h-[0px] transition-all duration-200"
                     data-last-ai="{{ $lastAiMessage }}"></div>

                {{-- Chat Input --}}
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-[#E5E7EB]">
                    <form id="chatForm" action="{{ route('chat.store', $session) }}" method="POST">
                        @csrf
                        <div class="flex gap-3">
                            <textarea
                                id="messageInput"
                                name="message"
                                rows="2"
                                placeholder="Ceritakan perasaanmu di sini…"
                                class="flex-1 border border-[#E5E7EB] rounded-2xl px-5 py-3 text-[#374151] placeholder-[#9CA3AF] focus:outline-none focus:ring-2 focus:ring-[#C084FC] focus:border-transparent resize-none"
                                maxlength="1000"
                                required
                            ></textarea>
                            <button type="submit" id="chatSubmitBtn" class="bg-[#C084FC] hover:bg-[#7E22CE] text-white px-6 py-3 rounded-full font-medium transition self-end shadow-sm hover:shadow-md">Kirim</button>
                        </div>
                        @error('message')
                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </form>
                    <div class="flex items-center justify-between mt-3">
                        <p class="text-xs text-[#9CA3AF]">Maksimal 1000 karakter</p>
                        <button type="button" id="triggerFinishModal" class="text-xs text-[#6B7280] hover:text-red-500 transition font-medium">Akhiri Sesi</button>
                    </div>
                </div>
            @endif

            {{-- Post-Chat Actionable Reflection Card --}}
            @if(session('show_reflection') && session('reflection_keywords'))
                <div class="bg-gradient-to-br from-purple-50 to-pink-50 border-2 border-[#E9D5FF] rounded-3xl p-8 mt-6 shadow-lg">
                    <div class="text-center mb-6">
                        <div class="text-5xl mb-3">🌟</div>
                        <h3 class="text-xl font-bold text-[#7E22CE]">Kartu Refleksi Akhir Sesi</h3>
                        <p class="text-sm text-[#6B7280] mt-1">Rangkuman perjalananmu bersama Hexa Space</p>
                    </div>

                    <div class="bg-white rounded-2xl p-5 mb-4 shadow-sm border border-[#E9D5FF]">
                        <h4 class="font-semibold text-[#374151] mb-2 flex items-center gap-2">
                            <span>📝</span> Topik yang Dibahas
                        </h4>
                        <div class="flex flex-wrap gap-2">
                            @foreach(session('reflection_keywords') as $keyword)
                                <span class="px-3 py-1.5 rounded-full text-xs font-medium bg-purple-100 text-purple-700">{{ $keyword }}</span>
                            @endforeach
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl p-5 mb-4 shadow-sm border border-[#E9D5FF]">
                        <h4 class="font-semibold text-[#374151] mb-3 flex items-center gap-2">
                            <span>💜</span> 3 Langkah Praktis untuk Kamu Coba
                        </h4>
                        <ol class="space-y-3">
                            @foreach(session('action_steps') as $index => $step)
                                <li class="flex items-start gap-3">
                                    <span class="w-7 h-7 rounded-full bg-[#C084FC] text-white text-sm flex items-center justify-center shrink-0 font-bold">{{ $index + 1 }}</span>
                                    <p class="text-sm text-[#374151]">{{ $step }}</p>
                                </li>
                            @endforeach
                        </ol>
                    </div>

                    <div class="text-center">
                        <p class="text-xs text-[#9CA3AF]">Terima kasih sudah memercayakan ceritamu kepada Hexa Space 💜</p>
                        <a href="{{ route('sessions.index') }}" class="inline-block mt-4 bg-[#C084FC] hover:bg-[#7E22CE] text-white px-6 py-2.5 rounded-full text-sm font-medium transition shadow-sm">Lihat Riwayat Sesi</a>
                    </div>
                </div>
            @endif

            {{-- Patient: Show doctor notes if session is finished --}}
            @if($session->status === 'finished' && auth()->user()->role !== 'doctor' && $session->doctor_notes)
                <div class="bg-purple-50 border border-purple-200 rounded-2xl p-6 mt-6">
                    <h3 class="font-semibold text-[#374151] mb-2">💜 Catatan dari Konselor</h3>
                    <p class="text-sm text-[#6B7280] whitespace-pre-line">{{ $session->doctor_notes }}</p>
                </div>
            @endif

            {{-- Doctor Notes --}}
            @if(auth()->user()->role === 'doctor')
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-[#E5E7EB] mt-6">
                    <h3 class="font-semibold text-[#374151] mb-3">Catatan Rekomendasi Klinis</h3>
                    <form action="{{ route('doctor.sessions.notes', $session) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <textarea name="doctor_notes" rows="4" class="w-full border border-[#E5E7EB] rounded-2xl px-5 py-3 text-[#374151] placeholder-[#9CA3AF] focus:outline-none focus:ring-2 focus:ring-[#C084FC] focus:border-transparent resize-none" placeholder="Tulis catatan klinis untuk pasien ini...">{{ old('doctor_notes', $session->doctor_notes) }}</textarea>
                        <div class="mt-3 text-right">
                            <button type="submit" class="bg-[#C084FC] hover:bg-[#7E22CE] text-white px-6 py-2 rounded-full text-sm font-medium transition">Simpan Catatan</button>
                        </div>
                    </form>
                </div>
            @endif
        </div>
    </div>

    {{-- Finish Modal --}}
    <div id="finishModal" class="fixed inset-0 z-50 hidden bg-black/40 flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl p-8 max-w-md w-full shadow-2xl">
            <div class="text-center mb-6">
                <div class="text-4xl mb-3">😊</div>
                <h3 class="text-xl font-bold text-[#374151]">Akhiri Sesi Konseling</h3>
                <p class="text-sm text-[#6B7280] mt-1">Bagaimana kondisi hatimu setelah bercerita di Hexa Space?</p>
            </div>
            <form id="finishForm" action="{{ route('sessions.finish', $session) }}" method="POST">
                @csrf
                @method('PATCH')
                <input type="hidden" name="final_mood" id="finalMood" value="">
                <div class="space-y-3">
                    <button type="button" onclick="submitFinish('lebih_tenang')" class="w-full flex items-center gap-4 p-4 border border-[#E5E7EB] rounded-2xl hover:border-purple-300 hover:bg-purple-50 transition text-left">
                        <span class="text-2xl">😊</span>
                        <div>
                            <p class="font-medium text-[#374151]">Lebih Tenang</p>
                            <p class="text-xs text-[#6B7280]">Aku merasa lebih baik setelah bercerita</p>
                        </div>
                    </button>
                    <button type="button" onclick="submitFinish('sama_saja')" class="w-full flex items-center gap-4 p-4 border border-[#E5E7EB] rounded-2xl hover:border-purple-300 hover:bg-purple-50 transition text-left">
                        <span class="text-2xl">😐</span>
                        <div>
                            <p class="font-medium text-[#374151]">Sama Saja</p>
                            <p class="text-xs text-[#6B7280]">Perasaanku masih sama seperti sebelumnya</p>
                        </div>
                    </button>
                    <button type="button" onclick="submitFinish('butuh_bantuan')" class="w-full flex items-center gap-4 p-4 border border-red-200 rounded-2xl hover:border-red-300 hover:bg-red-50 transition text-left">
                        <span class="text-2xl">🙁</span>
                        <div>
                            <p class="font-medium text-red-600">Masih Butuh Bantuan Ahli</p>
                            <p class="text-xs text-[#6B7280]">Aku merasa perlu berkonsultasi lebih lanjut</p>
                        </div>
                    </button>
                </div>
            </form>
            <div class="mt-4 text-center">
                <button type="button" onclick="closeFinishModal()" class="text-sm text-[#6B7280] hover:text-[#374151] transition">Batal</button>
            </div>
        </div>
    </div>

    <script>
        const emotionMap = {
            'anxious': { label: 'Cemas', bg: 'bg-yellow-100', text: 'text-yellow-700', emoji: '😰', ringColor: '#EAB308' },
            'sad': { label: 'Sedih', bg: 'bg-blue-100', text: 'text-blue-700', emoji: '😢', ringColor: '#3B82F6' },
            'burnout': { label: 'Lelah', bg: 'bg-gray-100', text: 'text-gray-600', emoji: '😩', ringColor: '#9CA3AF' },
            'crisis': { label: 'Butuh Bantuan', bg: 'bg-red-100', text: 'text-red-600', emoji: '🚨', ringColor: '#EF4444' },
            'neutral': { label: 'Tenang', bg: 'bg-purple-100', text: 'text-purple-700', emoji: '😊', ringColor: '#A855F7' },
        };

        const lastEmotion = '{{ session('last_emotion') }}';
        if (lastEmotion && emotionMap[lastEmotion]) {
            const indicator = document.getElementById('emotionIndicator');
            const info = emotionMap[lastEmotion];
            indicator.className = 'flex items-center gap-2 px-4 py-1.5 rounded-full text-xs font-medium transition-all duration-500 ' + info.bg + ' ' + info.text;
            indicator.innerHTML = '<span class="inline-block w-2.5 h-2.5 rounded-full" style="background: ' + info.ringColor + '; box-shadow: 0 0 8px ' + info.ringColor + '80, 0 0 16px ' + info.ringColor + '40;"></span> ' + info.emoji + ' ' + info.label;
            indicator.classList.remove('hidden');
        }

        const widgetType = '{{ session('widget_type') }}';
        if (widgetType === 'box_breathing') {
            document.getElementById('boxBreathingBanner').classList.remove('hidden');
            startBoxBreathing();
        } else if (widgetType === 'conflict_roadmap') {
            document.getElementById('conflictRoadmap').classList.remove('hidden');
        } else if (widgetType === 'addiction_recovery' || widgetType === 'addiction_barrier') {
            document.getElementById('addictionRecoveryRoadmap').classList.remove('hidden');
        } else if (widgetType === 'cbt_challenger') {
            document.getElementById('cbtChallengerWidget').classList.remove('hidden');
        }

        function startBoxBreathing() {
            const circle = document.getElementById('breathingCircle');
            const instruction = document.getElementById('breathingInstruction');
            const phase = document.getElementById('breathingPhase');
            const phases = [
                { text: 'Tarik Napas...', instruction: 'Tarik napas dalam-dalam melalui hidung...', scale: 1.5, duration: 4000 },
                { text: 'Tahan...', instruction: 'Tahan napasmu...', scale: 1.5, duration: 4000 },
                { text: 'Buang Napas...', instruction: 'Buang napas perlahan melalui mulut...', scale: 1, duration: 4000 },
                { text: 'Tahan...', instruction: 'Tahan napasmu sebelum menarik lagi...', scale: 1, duration: 4000 },
            ];
            let index = 0;
            function runPhase() {
                if (document.getElementById('boxBreathingBanner').classList.contains('hidden')) return;
                const p = phases[index];
                phase.textContent = p.text;
                instruction.textContent = p.instruction;
                circle.style.transform = 'scale(' + p.scale + ')';
                circle.style.transition = 'transform ' + p.duration + 'ms ease-in-out';
                for (let i = 1; i <= 4; i++) {
                    document.getElementById('phase' + i).className = 'w-3 h-3 rounded-full ' + (i === index + 1 ? 'bg-[#C084FC] scale-125' : 'bg-[#E9D5FF]');
                }
                index = (index + 1) % 4;
                setTimeout(runPhase, p.duration);
            }
            runPhase();
        }

        function closeBoxBreathing() {
            document.getElementById('boxBreathingBanner').classList.add('hidden');
        }

        function closeConflictRoadmap() {
            document.getElementById('conflictRoadmap').classList.add('hidden');
        }

        function closeAddictionRecovery() {
            document.getElementById('addictionRecoveryRoadmap').classList.add('hidden');
        }

        function closeCbtChallenger() {
            document.getElementById('cbtChallengerWidget').classList.add('hidden');
        }

        function cbtReframe(type) {
            const negative = document.getElementById('cbtNegativeThought');
            const challenge = document.getElementById('cbtChallengeText');
            const reframes = {
                semua: { neg: '"Aku bodoh dan tidak becus dalam segala hal"', pos: '"Kamu sedang belajar dan berkembang. Setiap ahli dulunya adalah pemula. Tidak apa-apa untuk tidak sempurna."' },
                percuma: { neg: '"Semua yang aku lakukan percuma dan sia-sia"', pos: '"Setiap langkah kecil yang kamu ambil hari ini adalah investasi untuk masa depanmu. Percayalah prosesnya."' },
                gagal: { neg: '"Aku selalu gagal dalam hidup, tidak ada yang berhasil"', pos: '"Kegagalan bukan akhir, tapi bagian dari proses menuju keberhasilan. Kamu pernah bertahan melewati masa-masa sulit sebelumnya."' },
            };
            const data = reframes[type] || reframes.semua;
            negative.textContent = data.neg;
            challenge.textContent = data.pos;
        }

        function fillPrediction(text) {
            const input = document.getElementById('messageInput');
            if (input) {
                input.value = text;
                input.focus();
                document.getElementById('predictiveSuggestions').innerHTML = '';
                input.dispatchEvent(new Event('input'));
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            const trigger = document.getElementById('triggerFinishModal');
            if (trigger) {
                trigger.addEventListener('click', function () {
                    document.getElementById('finishModal').classList.remove('hidden');
                });
            }

            const form = document.getElementById('chatForm');
            if (form) {
                form.addEventListener('submit', function () {
                    const btn = document.getElementById('chatSubmitBtn');
                    btn.disabled = true;
                    btn.innerHTML = '<svg class="animate-spin h-5 w-5 text-white mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
                    btn.classList.add('opacity-60', 'cursor-not-allowed');
                });
            }

            const input = document.getElementById('messageInput');
            if (input) {
                const predictDict = {
                    'hi': ['aku kehilangan uang 100 ribu punya mama', 'hidupku terasa berat dan menyakitkan'],
                    'ua': ['uang UKT kuliahku habis buat deposit', 'uang tabunganku ludes karena judi online'],
                    'pe': ['aku panik karena dompetku hilang di jalan', 'perasaanku hancur sejak kejadian itu'],
                    'du': ['uang UKT kuliahku habis buat deposit', 'aku ditinggal sendirian dalam kebingungan'],
                    'ju': ['aku kecanduan judi online di situs gacor', 'aku bingung cara bayar UKT setelah kalah judi'],
                    'on': ['bagaimana cara keluar dari jebakan judol?', 'aku sudah kehabisan akal karena judi online'],
                    'ga': ['aku kecanduan judi online di situs gacor', 'bagaimana cara berhenti dari kecanduan judi?'],
                    'de': ['aku tertipu ajakan teman untuk top-up judi', 'aku depresi karena semua uang hilang buat judi'],
                    'ng': ['bagaimana cara aku bilang ke mama?', 'aku bingung ngomong dari hati ke hati ke kakak'],
                    'bi': ['bagaimana cara aku bilang ke mama?', 'aku bingung harus mulai dari mana ceritanya'],
                    'ta': ['aku takut dimarahi orang tua karena berbuat salah', 'aku takut jujur ke kakak soal uang UKT habis'],
                    'ce': ['aku merasa cemas berlebihan tanpa alasan yang jelas', 'aku cemas menghadapi masa depan yang tidak pasti'],
                    'ja': ['jantungku berdegup kencang dan tanganku gemetar karena panik', 'aku tidak bisa tenang dan selalu gelisah'],
                    'an': ['pikiranku berisik dan overthinking setiap malam', 'selalu terbayang-bayang kesalahan masa lalu'],
                    'ka': ['aku tidak sanggup menghadapi semua ini', 'apakah yang aku alami ini normal?'],
                    'to': ['tolong bantu aku menemukan jalan keluar', 'aku butuh pegangan dan dukungan sekarang'],
                    'me': ['aku menyesal dan ingin bertanggung jawab', 'aku marah sama diri sendiri karena bodoh'],
                    'se': ['aku dihantui rasa bersalah setiap saat', 'apakah aku bisa sembuh dari trauma ini?'],
                    'ba': ['bagaimana caraku memulai percakapan dengan orang tua?', 'bagaimana cara mengatasi rasa bersalah ini?'],
                    'ra': ['rasanya ingin menyerah dan lari dari semuanya', 'aku tidak kuat menahan beban ini sendiri'],
                    'te': ['aku tidak kuat menjalani ini sendirian', 'teman-temanku menjauh sejak kejadian itu'],
                    'ke': ['aku kecewa dengan sikap keluargaku', 'kenapa semua ini terjadi padaku?'],
                    'sa': ['sakit hati rasanya dikhianati orang dekat', 'sakit kepala karena terlalu banyak beban pikiran'],
                    'pu': ['pusing memikirkan jalan keluar dari masalah ini', 'pusing karena tekanan tugas kuliah dan organisasi'],
                    'st': ['stres berat sampai tidak bisa tidur beberapa hari ini', 'stres menghadapi ekspektasi orang tua yang terlalu tinggi'],
                    'pi': ['pikiranku sangat berisik dan overthinking setiap malam', 'pikiran negatif terus menghantuiku tanpa henti'],
                    'be': ['beban hidup ini terasa sangat berat untukku', 'berat rasanya menjalani hari-hari yang monoton ini'],
                    'le': ['lelah mental rasanya ingin menghilang sebentar dari dunia', 'lelah dengan semua tuntutan hidup yang tidak ada habisnya'],
                    'ti': ['tidak bisa tidur karena overthinking memikirkan masa depan', 'tidak sanggup menghadapi tekanan dari keluarga'],
                    'in': ['ingin menangis tapi air mata sudah habis', 'ingin berhenti sejenak dari semua kepenatan ini'],
                    'ku': ['aku merasa sendiri meskipun dikelilingi banyak orang', 'aku ingin sembuh dan pulih dari trauma masa lalu'],
                    'mer': ['aku merasa cemas berlebihan tanpa alasan', 'aku merasa kesepian meskipun dikelilingi orang banyak'],
                    'ma': ['aku marah sama keadaan dan tidak tahu harus bagaimana', 'aku ingin marah tapi tidak tahu pada siapa'],
                };

                const contextSuggestionMap = {
                    'judi': ['aku ingin berhenti judi online tapi bingung caranya', 'aku kalah judi dan uang UKT habis semua'],
                    'judol': ['aku kecanduan judol dan tidak bisa berhenti', 'aku kalah judi dan uang UKT habis semua'],
                    'kecanduan': ['aku ingin berhenti judi online tapi bingung caranya', 'aku kecanduan judol dan tidak bisa berhenti'],
                    'uang': ['uang UKT kuliahku habis buat deposit', 'uang tabunganku ludes karena judi online'],
                    'ukt': ['uang UKT kuliahku habis buat deposit', 'uang tabunganku ludes karena judi online'],
                    'mama': ['bagaimana cara aku bilang ke mama?', 'aku takut dimarahi orang tua karena berbuat salah'],
                    'orang tua': ['bagaimana cara aku bilang ke mama?', 'aku takut dimarahi orang tua karena berbuat salah'],
                    'takut': ['aku takut dimarahi orang tua karena berbuat salah', 'aku takut jujur ke kakak soal uang UKT habis'],
                    'bohong': ['aku takut jujur tapi juga takut terus berbohong', 'bagaimana cara berhenti berbohong ke orang tua'],
                    'menyesal': ['aku menyesal dan ingin bertanggung jawab', 'aku menyesal telah melakukan semua ini'],
                    'sendiri': ['aku merasa sendiri dalam menghadapi semua ini', 'aku ingin sembuh dan tidak sendirian lagi'],
                    'lelah': ['lelah mental rasanya ingin menghilang sebentar', 'lelah dengan semua tuntutan hidup'],
                    'sedih': ['aku sedih dan tidak tahu harus cerita ke siapa', 'aku merasa hancur karena kehilangan'],
                    'cemas': ['aku merasa cemas berlebihan tanpa alasan yang jelas', 'aku cemas menghadapi masa depan yang tidak pasti'],
                    'overthinking': ['pikiranku berisik dan overthinking setiap malam', 'aku tidak bisa tidur karena overthinking'],
                    'tidur': ['aku tidak bisa tidur karena overthinking', 'stres berat sampai tidak bisa tidur beberapa hari'],
                    'stres': ['stres berat sampai tidak bisa tidur beberapa hari ini', 'stres menghadapi ekspektasi orang tua yang terlalu tinggi'],
                    'pusing': ['pusing memikirkan jalan keluar dari masalah ini', 'pusing karena tekanan tugas kuliah dan organisasi'],
                    'belum siap': ['aku belum siap untuk memaafkan diri sendiri', 'aku belum siap bicara dengan orang tua'],
                    'marah': ['aku marah sama keadaan dan tidak tahu harus bagaimana', 'aku marah sama diri sendiri karena bodoh'],
                    'payah': ['aku merasa payah dan tidak berguna', 'aku selalu gagal dalam apa pun yang aku lakukan'],
                };

                let debounceTimer = null;

                input.addEventListener('input', function () {
                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(function () {
                        const container = document.getElementById('predictiveSuggestions');
                        const val = input.value.toLowerCase().trim();

                        if (val.length < 2) {
                            container.innerHTML = '';
                            return;
                        }

                        const firstTwo = val.substring(0, 2);
                        const words = val.split(/\s+/);
                        const lastWord = words[words.length - 1];
                        const lastTwo = lastWord.length >= 2 ? lastWord.substring(0, 2) : '';
                        const lastAiRaw = container.getAttribute('data-last-ai') || '';
                        const lastAiContext = lastAiRaw.toLowerCase();

                        let matched = new Set();

                        if (predictDict[firstTwo]) {
                            predictDict[firstTwo].forEach(function (t) { matched.add(t); });
                        }
                        if (lastTwo && lastTwo !== firstTwo && predictDict[lastTwo]) {
                            predictDict[lastTwo].forEach(function (t) { matched.add(t); });
                        }

                        if (lastAiContext) {
                            for (const [topic, suggestions] of Object.entries(contextSuggestionMap)) {
                                if (lastAiContext.includes(topic)) {
                                    suggestions.forEach(function (s) {
                                        if (s.toLowerCase().includes(val)) {
                                            matched.add(s);
                                        }
                                    });
                                }
                            }
                        }

                        if (matched.size > 0) {
                            container.innerHTML = '';
                            matched.forEach(function (text) {
                                const btn = document.createElement('button');
                                btn.type = 'button';
                                btn.className = 'px-4 py-2.5 rounded-2xl text-sm bg-white hover:bg-[#FCE7F3] text-[#374151] border border-[#E9D5FF] hover:border-[#C084FC] transition-all shadow-sm hover:shadow-md';
                                btn.textContent = text;
                                btn.addEventListener('click', function () { fillPrediction(text); });
                                container.appendChild(btn);
                            });
                        } else {
                            container.innerHTML = '';
                        }
                    }, 100);
                });
            }
        });

        function submitFinish(mood) {
            document.getElementById('finalMood').value = mood;
            document.getElementById('finishForm').submit();
        }

        function closeFinishModal() {
            document.getElementById('finishModal').classList.add('hidden');
        }

        const chatContainer = document.getElementById('chatMessages');
        if (chatContainer) {
            chatContainer.scrollTop = chatContainer.scrollHeight;
        }
    </script>
</x-app-layout>
