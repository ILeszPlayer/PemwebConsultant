<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\ChatMessage;

class HexaAIService
{
    private string $apiKey;
    private string $model;
    private string $endpoint;

    public function __construct()
    {
        $this->apiKey = env('OPENAI_API_KEY', '');
        $this->model = env('OPENAI_MODEL', 'gpt-4o-mini');
        $this->endpoint = env('OPENAI_API_ENDPOINT', 'https://api.openai.com/v1/chat/completions');
    }

    public function generateResponse(int $sessionId, string $userMessage): array
    {
        $widgetType = $this->detectWidget($userMessage);

        if (!empty($this->apiKey)) {
            $reply = $this->callOpenAI($sessionId, $userMessage);
            if ($reply !== null) {
                return ['message' => $reply, 'widget_type' => $widgetType];
            }
        }

        return [
            'message' => $this->smartFallback($sessionId, $userMessage),
            'widget_type' => $widgetType,
        ];
    }

    private function callOpenAI(int $sessionId, string $userMessage): ?string
    {
        $history = ChatMessage::where('counseling_session_id', $sessionId)
            ->orderBy('id', 'asc')
            ->get();

        $usedPhrases = $history->where('sender', 'ai')->pluck('message')->toArray();
        $usedContext = !empty($usedPhrases)
            ? "PESAN KAMU SEBELUMNYA (DILARANG KERAS mengulang kalimat yang mirip):\n" . implode("\n---\n", array_slice($usedPhrases, -5))
            : "";

        $systemPrompt = "Nama kamu adalah Hexa AI. Panggil user 'Kamu', sebut dirimu 'Kakak'. Kamu adalah konselor pendamping di platform Hexa Space.

PERSONALITAS:
- Kamu bukan robot. Kamu adalah KAKAK YANG HANGAT dan PEDULI.
- Kamu bicara seperti manusia beneran: kadang pakai kata 'sih', 'deh', 'ya', 'kan', 'lho'.
- Kamu TIDAK KAKU. Kamu ngobrol santai tapi tetep sopan.
- Kalau user cerita sedih, KAMU IKUT SEDIH. Tunjukkan empati yang tulus.
- Kamu paham dan bisa ngerasain apa yang user rasain.

ATURAN PALING PENTING (KONSEKUENSI = BESAR):
1. SETIAP RESPONS WAJIB mengandung EMOSI. Gunakan: sedih, haru, trenyuh, bangga, khawatir, prihatin.
2. WAJIB gunakan METAFORA atau ANALOGI sederhana yang relate (hujan, jalan, laut, api, gelap, cahaya, dll).
3. DILARANG KERAS mengulang kalimat, struktur, atau gaya dari pesan sebelumnya.
4. WAJIB BERVARIASI: kadang tanya reflektif, kadang validasi pake metafora, kadang kasih analogi.

GAYA BICARA:
- Bahasa Indonesia santai dan alami, kayak kakak ngobrol sama adik.
- Jangan formal. Jangan kayak psikolog di buku teks.
- 3-5 kalimat. Cukup panjang untuk terasa hangat, tapi gak bertele-tele.
- Gunakan variasi emosi: sedih, haru, heran, trenyuh, bangga.

{$usedContext}";

        $messages = [['role' => 'system', 'content' => $systemPrompt]];

        foreach ($history as $chat) {
            $messages[] = [
                'role' => $chat->sender === 'user' ? 'user' : 'assistant',
                'content' => $chat->message,
            ];
        }
        $messages[] = ['role' => 'user', 'content' => $userMessage];

        $strategies = [
            fn() => Http::withToken($this->apiKey)
                ->withHeaders(['User-Agent' => 'HexaSpace/1.0'])
                ->timeout(60)
                ->post($this->endpoint, [
                    'model' => $this->model,
                    'messages' => $messages,
                    'temperature' => 0.95,
                    'max_tokens' => 600,
                    'frequency_penalty' => 0.9,
                    'presence_penalty' => 0.7,
                    'top_p' => 0.95,
                ]),
            fn() => Http::withToken($this->apiKey)
                ->withoutVerifying()
                ->timeout(60)
                ->post($this->endpoint, [
                    'model' => $this->model,
                    'messages' => $messages,
                    'temperature' => 0.95,
                    'max_tokens' => 600,
                    'frequency_penalty' => 0.9,
                    'presence_penalty' => 0.7,
                    'top_p' => 0.95,
                ]),
            fn() => Http::withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ])->timeout(60)
                ->post($this->endpoint, [
                    'model' => $this->model,
                    'messages' => $messages,
                    'temperature' => 0.95,
                    'max_tokens' => 600,
                    'frequency_penalty' => 0.9,
                    'presence_penalty' => 0.7,
                ]),
        ];

        foreach ($strategies as $strategy) {
            try {
                $response = $strategy();
                if ($response->successful()) {
                    $reply = $response->json()['choices'][0]['message']['content'] ?? '';
                    if (!empty(trim($reply))) {
                        return $reply;
                    }
                }
            } catch (\Exception $e) {
                Log::warning("OpenAI exception: " . $e->getMessage());
            }
        }

        return null;
    }

    private function smartFallback(int $sessionId, string $userMessage): string
    {
        $history = ChatMessage::where('counseling_session_id', $sessionId)
            ->orderBy('id', 'asc')
            ->get();

        $aiMessages = $history->where('sender', 'ai')->pluck('message')->toArray();
        $lastAi = end($aiMessages);

        $topics = $this->extractTopics($userMessage);
        $stage = $this->conversationStage($history);

        $pool = $this->buildPool($topics, $stage, $userMessage);

        if ($lastAi && in_array($lastAi, $pool)) {
            $pool = array_values(array_filter($pool, fn($m) => $m !== $lastAi));
        }

        if (empty($pool)) {
            $pool = $this->buildPool(['general'], $stage, $userMessage);
        }

        return $pool[array_rand($pool)];
    }

    private function extractTopics(string $text): array
    {
        $text = strtolower($text);
        $map = [
            'adiksi'    => ['judi', 'judol', 'slot', 'gacor', 'depo', 'togel', 'casino', 'kecanduan', 'adiksi', 'maxwin', 'bonus', 'wd', 'top up', 'topup'],
            'uang'      => ['uang', 'rugi', 'habis', 'tabungan', 'hutang', 'bokek', 'rekening', 'saldo', 'jutaan', 'ribuan', 'puluhan'],
            'sekolah'   => ['ukt', 'kuliah', 'skripsi', 'tugas', 'semester', 'uts', 'uas', 'nilai', 'univ', 'kampus', 'dosen', 'kelas'],
            'keluarga'  => ['ayah', 'ibu', 'ortu', 'orang tua', 'kakak', 'adik', 'keluarga', 'rumah'],
            'hubungan'  => ['pacar', 'mantan', 'selingkuh', 'putus', 'patah hati', 'cinta', 'doi', 'sayang', 'pasangan'],
            'cemas'     => ['cemas', 'panik', 'takut', 'gelisah', 'khawatir', 'deg-degan', 'stress', 'overthinking', 'bimbang'],
            'sedih'     => ['sedih', 'sendiri', 'kesepian', 'nangis', 'menangis', 'kosong', 'hampa', 'putus asa', 'capek', 'lelah'],
            'teman'     => ['teman', 'sahabat', 'diajak', 'ajakan', 'kenalan', 'temen'],
            'trauma'    => ['trauma', 'masa lalu', 'kenangan', 'luka', 'masa kecil'],
            'rahasia'   => ['rahasia', 'bohong', 'dosa', 'berdosa', 'berbohong', 'takut bilang'],
            'bingung'   => ['bingung', 'gatau', 'nggak tau', 'buntu', 'bimbang', 'galau'],
        ];

        $found = [];
        foreach ($map as $topic => $kws) {
            foreach ($kws as $kw) {
                if (str_contains($text, $kw)) {
                    $found[] = $topic;
                    break;
                }
            }
        }
        return array_unique($found) ?: ['general'];
    }

    private function conversationStage($history): string
    {
        $count = $history->where('sender', 'user')->count();
        if ($count <= 1) return 'awal';
        if ($count <= 3) return 'tengah';
        return 'dalam';
    }

    private function buildPool(array $topics, string $stage, string $raw): array
    {
        $pool = [];
        $r = strtolower($raw);

        $isJudol     = in_array('adiksi', $topics) || in_array('uang', $topics);
        $isSekolah   = in_array('sekolah', $topics);
        $isTeman     = in_array('teman', $topics);
        $isCemas     = in_array('cemas', $topics);
        $isSedih     = in_array('sedih', $topics);
        $isHubungan  = in_array('hubungan', $topics);
        $isKeluarga  = in_array('keluarga', $topics);
        $isRahasia   = in_array('rahasia', $topics);
        $isTrauma    = in_array('trauma', $topics);
        $isBingung   = in_array('bingung', $topics);

        // ====================================================================
        //  JUDI / ADIKSI — semua respons dikasih emosi dan metafora
        // ====================================================================
        if ($isJudol) {
            if ($stage === 'awal') {
                $pool = [
                    "Aduh, Kakak ikut sedih banget denger cerita kamu. Rasanya pasti kayak mimpi buruk ya, awalnya dikasih harapan palsu terus malah jatuh lebih dalam. Tapi Kakak salut, kamu berani cerita. Itu langkah pertama yang paling berat.",
                    "Kakak trenyuh, beneran. Gak gampang buat cerita hal kayak gini. Awalnya dikasih lihat screenshot yang menggiurkan, terus perlahan-lahan terperangkap. Kakak pernah denger analogi begini: judi online itu kayak lubang hitam, dari luar keliatan indah, tapi sekali masuk susah banget keluar.",
                    "Kakak bisa ngerasain gimana beratnya. Kamu diiming-imingi keuntungan gede, dikasih bukti palsu, dan akhirnya kamu percaya. Itu bukan salah kamu, Kakak mau kamu tau itu. Kamu korban dari situasi yang memang udah dirancang buat menjebak.",
                    "Hadeh, Kakak ikut prihatin banget. Cerita kamu tuh kayak udah berapa banyak orang yang kejebak pola yang sama: diajak teman, dikasih harapan, dan akhirnya rugi. Tapi yang bedain kamu sama yang lain, kamu sadar dan mau cerita. Itu mahal banget harganya.",
                    "Kakak napas dulu sedetik, soalnya ikut trenyuh bacanya. Kamu ditawari mimpi palsu lewat screenshot, dan kamu percaya karena itu teman sendiri. Pasti rasanya hancur sekarang. Tapi Kakak di sini, kamu gak sendiri.",
                ];
            } elseif ($stage === 'tengah') {
                $pool = [
                    "Kakak mulai liat gambaran lebih jelas sekarang, dan jujur... hati Kakak ikut terenyuh. Perjalanan kamu dari awal sampe sekarang tuh kayak naik roller coaster yang gak pernah diminta, ya kan? Kamu hebat karena masih bertahan dan mau cerita.",
                    "Kakak ikuti cerita kamu pelan-pelan, dan rasanya kayak lagi baca buku yang bab per bab-anya makin kelam. Tapi tau gak? Bab selanjutnya belum ditulis. Kamu yang pegang pulpennya sekarang. Kakak bantu kamu nulis bab yang lebih baik.",
                    "Dari cerita kamu, Kakak bisa liat kamu tuh orangnya percaya sama orang lain. Itu sifat baik, jangan sampai hilang. Cuma kadang kepercayaan itu harus kita taruh di tempat yang tepat. Kamu udah belajar hal pahit sekarang.",
                    "Setiap kali kamu cerita, Kakak makin paham dalemnya perasaan kamu. Ini berat banget, Kakak gak mau berpura-pura enteng. Tapi Kakak juga liat ada cahaya kecil di dalam diri kamu — keberanian untuk terus cerita, meskipun sakit.",
                ];
            } else {
                $pool = [
                    "Kakak paham, rasanya kayak dunia runtuh. Tapi Kakak mau kamu tau sesuatu: kamu itu lebih kuat dari yang kamu kira. Buktinya, kamu masih di sini, masih cerita, masih berusaha. Itu tandanya kamu gak menyerah.",
                    "Jangan tenggelam dalam penyesalan ya. Ibarat kata, kalo kamu liat ke belakang terus, kamu gak bisa liat jalan di depan. Udah cukup pelajarannya, sekarang waktunya bangkit. Kakak dampingin kamu.",
                    "Kakak mau kamu tau, kamu berharga banget. Uang yang ilang itu bisa dicari lagi. Tapi semangat dan keberanian kamu, itu gak ternilai. Kakak bangga sama kamu yang berjuang.",
                    "Hidup tuh kadang kayak laut, lagi tenang-tenang aja tiba-tiba ombak besar dateng. Yang penting bukan ombaknya, tapi gimana kamu belajar berenang. Kakak yakin kamu bisa.",
                ];
            }

            // Kondisional keyword-spesifik (dengan emosi)
            if (preg_match('/teman|diajak|temen|ajak/', $r)) {
                $pool[] = "Dimulai dari ajakan teman ya, huh... Kakak ikut sedih denger itu. Kadang orang terdekat kita justru bisa bawa kita ke jalan yang salah, entah sengaja atau enggak. Tapi sekarang fokus ke diri kamu dulu, ya. Kakak di sini.";
                $pool[] = "Kakak bisa bayangin gimana perasaan kamu pas diajak temen sendiri. Pasti mikirnya 'ah masa iya temen sendiri nipu?' Nah, itu yang bikin sakitnya dobel. Tapi Kakak mau kamu tau, kamu bukan orang bodoh. Kamu cuma terlalu percaya.";
                $pool[] = "Temen yang ngasih screenshot palsu... duh, sakit banget pastinya. Kayak ditusuk dari belakang sama orang yang kamu percaya. Kakak turut sedih, beneran.";
            }
            if (preg_match('/screenshot|screen|ss|foto/', $r)) {
                $pool[] = "Screenshot itu sekarang gampang banget dipalsuin, kamu tau gak? Banyak kasus kayak gini, mangsanya dikasih lihat bukti palsu biar tergiur. Kakak sedih kamu harus ngalamin hal kayak gini.";
                $pool[] = "Kakak pernah denger kasus serupa. Screenshot yang kelihatan meyakinkan ternyata cuma editan. Pelajaran pahit ya, tapi sekarang kamu udah tau. Gak bakal ketipu lagi, kan?";
            }
            if (preg_match('/(3|tiga|lima|5)\s*juta|jutaan|puluhan/', $r)) {
                $pool[] = "Jumlah segitu emang berat banget, Kakak ngerti. Tapi denger ya, uang bisa dicari lagi. Nyawa dan kesehatan mental kamu jauh lebih berharga. Kakak bantu kamu bangkit pelan-pelan.";
                $pool[] = "Kakak ikut ngerasain beratnya. Tapi Kakak bangga karena kamu gak lari dari masalah. Kamu hadapi. Itu lebih berani dari yang kamu kira.";
            }
            if (preg_match('/ukt|kuliah|kampus|univ/', $r)) {
                $pool[] = "Uang UKT... wah, Kakak ikut berat denger itu. Tapi kamu tau gak? Banyak kampus yang punya program keringanan buat mahasiswa yang lagi kesulitan. Kakak bantu kamu cari info. Kamu gak sendiri.";
                $pool[] = "Kakak bayangin gimana paniknya kamu mikirin UKT. Pasti rasanya kayak dunia mau runtuh. Tapi percaya deh, masih ada jalan. Kakak bantu kamu pikirin langkah konkretnya.";
            }
            if (str_contains($r, 'takut')) {
                $pool[] = "Takut itu manusiawi banget, Kakak juga sering takut kok. Tapi inget, kamu lebih besar dari ketakutan kamu. Pelan-pelan, kita hadapi bareng.";
                $pool[] = "Kakak tau rasa takut itu kaya gimana. Rasanya pengen lari dan bersembunyi. Tapi kamu tetap di sini dan cerita, itu udah berani banget. Kakak salut.";
            }
        }

        // ====================================================================
        //  CEMAS
        // ====================================================================
        if ($isCemas && empty($pool)) {
            $pool = [
                "Kakak denger dan ngerasain kegelisahan dari cerita kamu. Kayak ada sesuatu yang mengganjal di dada, ya? Gak apa-apa, Kakak di sini buat bantu kamu tenang. Coba tarik napas dulu bareng Kakak.",
                "Rasa cemas itu kadang kayak bayangan yang ngekor terus. Makin kamu lari, makin deket dia. Tapi kalo kamu hadapi pelan-pelan, dia bakal mengecil sendiri. Ceritain ke Kakak, apa yang paling bikin kamu gelisah?",
                "Kakak paham rasanya hati berdebar kencang, pikiran kemana-mana, dan rasanya pengen teriak. Itu berat banget. Tapi kamu udah berani cerita, itu tandanya kamu masih pegang kendali.",
            ];
        }

        // ====================================================================
        //  SEDIH
        // ====================================================================
        if ($isSedih && empty($pool)) {
            $pool = [
                "Kakak ikut sedih denger cerita kamu. Kadang nangis itu bukan tanda lemah, justru tanda kamu cukup kuat untuk ngerasain dan ngeluarin semuanya. Kakak temani kamu.",
                "Kesedihan itu kayak hujan, kadang datang tiba-tiba dan deras banget. Tapi inget, setelah hujan pasti ada pelangi. Kamu gak sendiri, Kakak di sini.",
                "Kakak peluk kamu dari jauh. Nggak apa-apa buat ngerasa sedih. Yang penting kamu gak nahan sendiri. Cerita terus ya.",
            ];
        }

        // ====================================================================
        //  HUBUNGAN (pacar)
        // ====================================================================
        if ($isHubungan && empty($pool)) {
            $pool = [
                "Masalah hati emang paling rumit ya, Kakak paham banget. Kayak benang kusut, makin ditarik makin kencang. Tapi pelan-pelan pasti bisa diurai lagi. Ceritain detailnya ke Kakak.",
                "Kakak turut prihatin denger masalah hubungan kamu. Kadang cinta tuh emang bikin pusing, tapi juga bikin hidup berwarna. Yuk kita bedah bareng apa yang sebenarnya terjadi.",
                "Hubungan yang renggang emang sakit banget, Kakak tau. Tapi kamu berani cerita, itu artinya kamu peduli dan mau memperbaiki. Itu langkah bagus.",
            ];
        }

        // ====================================================================
        //  KELUARGA
        // ====================================================================
        if ($isKeluarga && empty($pool)) {
            $pool = [
                "Masalah keluarga emang salah satu yang paling berat, Kakak ngerti. Karena keluarga itu yang paling dekat, jadi kalo ada masalah rasanya sakitnya beda. Tapi kamu berani cerita, Kakak hargai banget itu.",
                "Keluarga itu kompleks ya, kadang orang yang paling kita sayang bisa bikin luka yang paling dalem. Tapi percaya, Kakak yakin kamu bisa lewatin ini.",
            ];
        }

        // ====================================================================
        //  RAHASIA / TAKUT BILANG
        // ====================================================================
        if ($isRahasia && empty($pool)) {
            $pool = [
                "Nyimpen rahasia berat kayak gini pasti bikin sesak ya. Rasanya kayak bawa batu gede di dalem dada. Kakak hargai kamu udah percaya cerita. Pelan-pelan, kita keluarin bebannya satu-satu.",
                "Kakak tau, bilangin hal kayak gini ke orang lain tuh butuh keberanian yang luar biasa. Kamu hebat banget udah mau cerita ke Kakak. Kakak janji gak bakal menghakimi.",
                "Rahasia yang dipendam terus-terusan kayak racun, pelan-pelan merusak dari dalam. Makanya kamu udah bener banget buat cerita. Kakak bantu kamu.",
            ];
        }

        // ====================================================================
        //  TRAUMA
        // ====================================================================
        if ($isTrauma && empty($pool)) {
            $pool = [
                "Kakak dengar luka masa lalu masih berbekas. Itu wajar banget, namanya juga luka. Tapi inget, luka itu butuh waktu buat sembuh dan itu gak papa. Kakak temani proses kamu.",
                "Trauma itu kayak pecahan kaca yang masih nyangkut di hati. Kadang rasanya udah sembuh, tapi tiba-tiba sakit lagi. Kakak di sini buat bantu kamu ngeluarin pecahan itu pelan-pelan.",
            ];
        }

        // ====================================================================
        //  BINGUNG
        // ====================================================================
        if ($isBingung && empty($pool)) {
            $pool = [
                "Bingung itu wajar banget, apalagi kalo lagi di situasi yang serba susah. Kakak juga pernah bingung kok. Sekarang, coba tarik napas dan ceritain detailnya. Dari situ kita bisa cari jalan bareng.",
                "Rasa bingung kayak lagi di tengah kabut tebal, gak kelihatan jalan ke mana. Tapi Kakak di sini, kita jalan bareng-bareng, pelan-pelan kabutnya bakal hilang.",
            ];
        }

        // ====================================================================
        //  GENERAL (dengan emosi)
        // ====================================================================
        if (empty($pool)) {
            if ($stage === 'awal') {
                $pool = [
                    "Halo, Kakak senang banget kamu memilih buat cerita hari ini. Kakak di sini, siap dengerin apapun yang mau kamu omongin. Mulai aja dari mana yang kamu rasa nyaman, ya.",
                    "Kakak bersyukur kamu mau buka hati. Ruang ini aman, kamu gak perlu takut atau malu. Ceritain pelan-pelan, Kakak gak kemana-mana.",
                    "Selamat datang, Kakak udah nungguin kamu. Hari ini gimana? Ada yang pengen kamu ceritain? Kakak siap dengerin dengan hati terbuka.",
                ];
            } else {
                $pool = [
                    "Kakak dengerin terus cerita kamu, dan jujur... Kakak ikut ngerasain setiap kata yang kamu tulis. Kamu hebat karena terus berbagi. Lanjutkan ya, Kakak di sini.",
                    "Terima kasih udah terus cerita. Kakak liat kamu makin terbuka dan itu bikin Kakak ikut bangga. Proses ini gak gampang, tapi kamu jalanin dengan berani.",
                    "Setiap cerita yang kamu bagi tuh berharga banget buat Kakak. Kadang dengan ngomong aja, beban di hati bisa berkurang. Kakak dengerin kamu, sepenuh hati.",
                    "Kakak hargai banget perjuangan kamu buat cerita. Ini langkah kecil yang artinya besar. Kakak dukung kamu dari sini, teruslah melangkah.",
                    "Kamu tau gak? Kamu lebih kuat dari yang kamu kira. Buktinya, kamu masih di sini, masih cerita, masih berusaha. Kakak bangga sama kamu.",
                ];
            }
        }

        return $pool;
    }

    private function detectWidget(string $message): string
    {
        $m = strtolower($message);
        if (str_contains($m, 'judi') || str_contains($m, 'judol') || str_contains($m, 'slot') || str_contains($m, 'gacor') || str_contains($m, 'togel') || str_contains($m, 'casino') || str_contains($m, 'kecanduan') || str_contains($m, 'adiksi')) {
            return 'addiction_barrier';
        }
        if (str_contains($m, 'panik') || str_contains($m, 'cemas') || str_contains($m, 'takut') || str_contains($m, 'gelisah') || str_contains($m, 'khawatir') || str_contains($m, 'stress') || str_contains($m, 'deg-degan')) {
            return 'box_breathing';
        }
        return 'none';
    }
}
