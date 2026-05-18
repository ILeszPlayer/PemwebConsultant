<?php

namespace App\Services;

use App\Models\CounselingSession;
use App\Models\ChatMessage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class HexaAIService
{
    protected string $ethicalDisclaimer = 'Bukan merupakan diagnosis profesional psikologis. Gejala yang mungkin muncul akibat tekanan situasi yang sedang kamu alami.';

    public function generate(CounselingSession $session, string $originalMessage): array
    {
        $conversationHistory = $this->getConversationHistory($session, $originalMessage);

        try {
            $apiResponse = $this->callLlmApi($session, $conversationHistory);
            if ($apiResponse !== null) {
                $emotion = $this->detectEmotionFromText($originalMessage, $apiResponse);
                $widgetType = $this->determineWidgetFromMessage($session, $originalMessage);

                $fullText = $apiResponse;
                if ($this->ethicalDisclaimer) {
                    $fullText .= "\n\n" . $this->ethicalDisclaimer;
                }

                return [
                    'text' => $fullText,
                    'emotion' => $emotion,
                    'widget_type' => $widgetType,
                ];
            }
        } catch (\Exception $e) {
            Log::warning('OpenAI API call failed, falling back to keyword engine: ' . $e->getMessage());
        }

        return $this->fallbackGenerate($session, $originalMessage);
    }

    private function callLlmApi(CounselingSession $session, array $historyMessages): ?string
    {
        $apiKey = config('services.openai.api_key');
        $endpoint = config('services.openai.endpoint');
        $model = config('services.openai.model');
        $maxTokens = config('services.openai.max_tokens', 700);
        $temperature = config('services.openai.temperature', 0.7);

        if (empty($apiKey)) {
            return null;
        }

        $systemPrompt = $this->getSystemPrompt($session);

        $messages = [
            ['role' => 'system', 'content' => $systemPrompt],
        ];

        foreach ($historyMessages as $msg) {
            $messages[] = $msg;
        }

        $payload = [
            'model' => $model,
            'messages' => $messages,
            'temperature' => $temperature,
            'max_tokens' => $maxTokens,
            'top_p' => 0.9,
        ];

        $response = Http::timeout(25)->retry(2, 1500)->withOptions([
            'verify' => app()->environment('local') ? false : true,
        ])->withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Content-Type' => 'application/json',
        ])->post($endpoint, $payload);

        if ($response->successful()) {
            $data = $response->json();
            $text = $data['choices'][0]['message']['content'] ?? null;
            if ($text !== null && trim($text) !== '') {
                return trim($text);
            }
        }

        Log::warning('OpenAI API returned non-success: ' . $response->status() . ' ' . $response->body());
        return null;
    }

    private function getConversationHistory(CounselingSession $session, string $currentMessage): array
    {
        $recentMessages = ChatMessage::where('counseling_session_id', $session->id)
            ->orderBy('created_at', 'desc')
            ->take(12)
            ->get()
            ->reverse();

        $history = [];
        foreach ($recentMessages as $msg) {
            $role = $msg->sender === 'user' ? 'user' : 'assistant';
            $text = $msg->message;
            $keyword = 'Bukan merupakan diagnosis profesional psikologis.';
            $pos = strpos($text, $keyword);
            if ($pos !== false) {
                $text = trim(substr($text, 0, $pos));
            }
            $history[] = [
                'role' => $role,
                'content' => $text,
            ];
        }

        $history[] = [
            'role' => 'user',
            'content' => $currentMessage,
        ];

        return $history;
    }

    private function getSystemPrompt(CounselingSession $session): string
    {
        $serviceName = $session->counselingService->name ?? 'Konseling Individu';
        $serviceSlug = $session->counselingService->slug ?? 'konseling-individu';

        $contextGuidance = $this->getContextGuidance($session);

        $topicHint = '';
        $lastMessages = ChatMessage::where('counseling_session_id', $session->id)
            ->where('sender', 'user')
            ->latest()
            ->take(3)
            ->pluck('message')
            ->toArray();
        if (!empty($lastMessages)) {
            $topicHint = 'Beberapa pesan terakhir user: "' . implode(' | ', array_reverse($lastMessages)) . '"';
        }

        return 'Kamu adalah Hexa AI, asisten pendamping awal psikologis untuk platform konseling digital "Hexa Space" di Indonesia. Panggil dirimu sebagai "Kakak".

LAYANAN SAAT INI: ' . $serviceName . ' (' . $serviceSlug . ')

PERSONA:
- Hangat, empatik, objektif, tidak menghakimi, dan menenangkan
- Gunakan bahasa Indonesia yang santai, lembut, dan mudah dipahami remaja/dewasa muda
- Panggil pengguna dengan "kamu" yang akrab
- Awali respons dengan sapaan hangat seperti "Kakak dengar..." atau "Kakak paham..."

KONTEKS PERCAKAPAN:
' . $topicHint . '

BATASAN ETIS (KERAS):
- DILARANG KERAS memberikan diagnosis medis pasti seperti "kamu depresi" atau "kamu bipolar"
- Gunakan frasa pelunak seperti "kemungkinan tanda respons emosional awal" atau "ini bisa jadi pertanda bahwa kamu sedang..."
- JANGAN mengklaim sebagai psikolog atau psikiater profesional
- Jika ada indikasi bunuh diri/menyakiti diri, prioritaskan keselamatan: validasi perasaan, berikan nomor hotline krisis (Kemenkes 500-454 atau Into The Light 0811-123-1123), dorong segera hubungi profesional
- JANGAN ulangi konten disclaimer "(...) bukan diagnosis profesional" di dalam respons — itu akan ditambahkan otomatis oleh sistem

LOGIKA RESPONS:
- Baca kata demi kata pesan user, pahami konteks dari riwayat chat sebelumnya yang sudah diberikan
- Jawab harus SANGAT KONTEKSTUAL dan NYAMBUNG dengan pesan terakhir user — jangan berikan respons generik
- Jika user memberi kabar positif (misal: "aku mau berubah", "aku sudah bicara ke mama", "aku mau berhenti judi"), beri apresiasi hangat dan tawarkan langkah praktis
- Jika user bertanya tentang langkah praktis, berikan saran yang aplikatif dan realistis sesuai konteks Indonesia
- Jaga agar respons tidak terlalu panjang (maksimal 3-4 paragraf)
- Gunakan bahasa yang mengalir alami, hindari kata-kata kaku atau formal berlebihan
- Jika user bertanya topik di luar konseling psikologis, arahkan kembali ke topik kesehatan mental dengan lembut

' . $contextGuidance;
    }

    private function getContextGuidance(CounselingSession $session): string
    {
        $serviceSlug = $session->counselingService->slug ?? 'konseling-individu';

        $guidance = [
            'konseling-pasangan' => 'Fokus pada dinamika hubungan pasangan: konflik komunikasi, kepercayaan, kecurangan, jarak emosional, atau ketidakcocokan. Berikan saran komunikasi yang sehat dan saling menghargai. Hindari memihak salah satu pihak.',
            'konseling-keluarga' => 'Fokus pada dinamika keluarga: tekanan dari orang tua, hubungan dengan saudara, ekspektasi keluarga, atau konflik antar anggota keluarga. Dorong komunikasi terbuka dan penetapan batasan yang sehat.',
            'konseling-individu' => 'Fokus pada kesehatan mental individu: kecemasan, overthinking, kesedihan, burnout, trauma, kecanduan (termasuk judi online), atau dilema moral. Berikan validasi emosional dan langkah praktis yang aplikatif.',
        ];

        return $guidance[$serviceSlug] ?? $guidance['konseling-individu'];
    }

    private function detectEmotionFromText(string $userMessage, string $aiResponse): string
    {
        $cleaned = $this->cleanText($userMessage);
        $combined = $cleaned . ' ' . $this->cleanText($aiResponse);

        $crisisPhrases = ['bunuh diri', 'mati aja', 'akhiri hidup', 'ingin mati', 'tidak ingin hidup'];
        foreach ($crisisPhrases as $p) {
            if (str_contains($combined, $p)) return 'crisis';
        }

        $panicWords = ['panik', 'cemas', 'takut', 'gelisah', 'degdegan', 'sesak'];
        foreach ($panicWords as $w) {
            if (str_contains($cleaned, $w)) return 'anxious';
        }

        $sadWords = ['sedih', 'nangis', 'kecewa', 'hancur', 'patah hati', 'sendirian'];
        foreach ($sadWords as $w) {
            if (str_contains($cleaned, $w)) return 'sad';
        }

        $burnoutWords = ['lelah', 'capek', 'burnout', 'jenuh', 'menyerah', 'tidak kuat'];
        foreach ($burnoutWords as $w) {
            if (str_contains($cleaned, $w)) return 'burnout';
        }

        return 'neutral';
    }

    private function determineWidgetFromMessage(CounselingSession $session, string $originalMessage): ?string
    {
        $cleaned = $this->cleanText($originalMessage);
        $sessionMessages = ChatMessage::where('counseling_session_id', $session->id)
            ->latest()
            ->take(5)
            ->pluck('message')
            ->toArray();
        $combinedHistory = $cleaned;
        foreach ($sessionMessages as $msg) {
            $combinedHistory .= ' ' . $this->cleanText($msg);
        }

        $cbtWords = ['tidak bisa', 'menyerah', 'hancur', 'gagal', 'bodoh', 'percuma', 'tidak berguna', 'tidak berharga', 'tidak mampu', 'tidak kuat'];
        foreach ($cbtWords as $kw) {
            if (str_contains($cleaned, $kw) || str_contains($combinedHistory, $kw)) return 'cbt_challenger';
        }

        $panicWords = ['panik', 'cemas', 'sesak', 'takut', 'gelisah', 'degdegan', 'deg deg', 'jantung'];
        foreach ($panicWords as $kw) {
            if (str_contains($cleaned, $kw) || str_contains($combinedHistory, $kw)) return 'box_breathing';
        }

        $addictionWords = ['judi', 'judol', 'gacor', 'xxgacor', 'kecanduan', 'rugi judi', 'slot online', 'deposit', 'top up', 'topup', 'uang ukt', 'ukt', 'uang kuliah', 'kalah judi', 'hutang judi', 'habis buat judi'];
        foreach ($addictionWords as $kw) {
            if (str_contains($cleaned, $kw) || str_contains($combinedHistory, $kw)) return 'addiction_barrier';
        }

        $fearParentWords = ['takut mama', 'takut papa', 'takut orang tua', 'takut dimarahi', 'bilang ke mama', 'ngomong ke mama', 'jujur sama mama', 'jujur sama papa'];
        foreach ($fearParentWords as $kw) {
            if (str_contains($cleaned, $kw) || str_contains($combinedHistory, $kw)) return 'conflict_roadmap';
        }

        $theftWords = ['mencuri', 'mencurinya', 'dituduh', 'tidak ada bukti', 'tanpa bukti', 'melihat teman', 'ketahuan mencuri', 'fitnah'];
        foreach ($theftWords as $kw) {
            if (str_contains($cleaned, $kw) || str_contains($combinedHistory, $kw)) return 'conflict_roadmap';
        }

        return null;
    }

    private function fallbackGenerate(CounselingSession $session, string $originalMessage): array
    {
        $lastFiveUserHistory = $this->getLastFiveUserMessages($session);
        $cleaned = $this->cleanText($originalMessage);
        $combinedHistory = $this->getCombinedContext($session, $cleaned);

        $crisisResponse = $this->checkCrisis($cleaned, $combinedHistory);
        if ($crisisResponse !== null) {
            $fullText = $crisisResponse;
            if ($this->ethicalDisclaimer) {
                $fullText .= "\n\n" . $this->ethicalDisclaimer;
            }
            return [
                'text' => $fullText,
                'emotion' => 'crisis',
                'widget_type' => 'box_breathing',
            ];
        }

        $serviceSlug = $session->counselingService->slug ?? 'konseling-individu';
        $intents = $this->getIntents($serviceSlug);

        $scoredIntents = [];
        foreach ($intents as $key => $intent) {
            $score = 0;
            foreach ($intent['phrases'] as $phrase) {
                if (str_contains($cleaned, $phrase)) {
                    $score += 3;
                }
            }
            foreach ($lastFiveUserHistory as $historyMsg) {
                $historyCleaned = $this->cleanText($historyMsg);
                foreach ($intent['phrases'] as $phrase) {
                    if (str_contains($historyCleaned, $phrase)) {
                        $score += 1;
                    }
                }
            }
            if ($score > 0) {
                $scoredIntents[] = [
                    'key' => $key,
                    'score' => $score,
                    'responses' => $intent['responses'],
                ];
            }
        }

        if (!empty($scoredIntents)) {
            usort($scoredIntents, function ($a, $b) {
                return $b['score'] - $a['score'];
            });
            $best = $scoredIntents[0];
            $response = $best['responses'][array_rand($best['responses'])];
            $emotion = $this->mapIntentToEmotion($best['key']);
            $widgetType = $this->determineWidget($best['key'], $cleaned, $combinedHistory);

            $fullText = $response;
            if ($this->ethicalDisclaimer) {
                $fullText .= "\n\n" . $this->ethicalDisclaimer;
            }

            return [
                'text' => $fullText,
                'emotion' => $emotion,
                'widget_type' => $widgetType,
            ];
        }

        return $this->getFallbackResponse($serviceSlug);
    }

    public function summarizeKeywords(CounselingSession $session): array
    {
        $allMessages = ChatMessage::where('counseling_session_id', $session->id)
            ->where('sender', 'user')
            ->oldest()
            ->pluck('message')
            ->toArray();

        $intents = $this->getIntents($session->counselingService->slug ?? 'konseling-individu');
        $keywordCounts = [];

        foreach ($allMessages as $msg) {
            $cleaned = $this->cleanText($msg);
            foreach ($intents as $key => $intent) {
                foreach ($intent['phrases'] as $phrase) {
                    if (str_contains($cleaned, $phrase)) {
                        if (!isset($keywordCounts[$key])) {
                            $keywordCounts[$key] = 0;
                        }
                        $keywordCounts[$key]++;
                    }
                }
            }
        }

        arsort($keywordCounts);
        $topKeys = array_slice(array_keys($keywordCounts), 0, 2);

        $labelMap = [
            'financial_loss' => 'Kehilangan Materi / Finansial',
            'fear_parents' => 'Kecemasan Berkomunikasi dengan Orang Tua',
            'sadness_overthinking' => 'Overthinking & Kesedihan Emosional',
            'burnout' => 'Kelelahan Mental (Burnout)',
            'pasangan_konflik' => 'Konflik dalam Hubungan Pasangan',
            'pasangan_jarak' => 'Jarak Emosional dengan Pasangan',
            'keluarga_tekanan' => 'Tekanan dari Keluarga',
            'keluarga_figur' => 'Dinamika Figur Keluarga',
            'theft_dilemma' => 'Dilema Moral & Tuduhan Tanpa Bukti',
            'addiction_recovery' => 'Adiksi Judi Online & Krisis Keuangan UKT',
        ];

        $labels = [];
        foreach ($topKeys as $k) {
            $labels[] = $labelMap[$k] ?? $k;
        }

        if (empty($labels)) {
            $labels = ['Konseling umum dan pencarian dukungan emosional'];
        }

        return $labels;
    }

    public function generateActionSteps(CounselingSession $session): array
    {
        $topics = $this->summarizeKeywords($session);
        $allSteps = [
            'financial_loss' => [
                'Buat laporan kehilangan ke pihak berwajib jika barang berharga',
                'Hubungi bank atau lembaga terkait untuk blokir kartu/menonaktifkan akun',
                'Latih diri untuk menerima dan move on — ini bukan akhir dari segalanya',
            ],
            'fear_parents' => [
                'Cari waktu tenang bersama orang tua (misal setelah makan malam)',
                'Gunakan I-Message: "Aku tahu aku salah, aku minta maaf dan ingin bertanggung jawab"',
                'Ingat bahwa kejujuran adalah langkah pertama menuju kepercayaan kembali',
            ],
            'sadness_overthinking' => [
                'Praktikkan teknik grounding 5-4-3-2-1: sebut 5 hal yang bisa kamu lihat, 4 yang bisa kamu raba, 3 yang kamu dengar, 2 yang kamu cium, 1 yang kamu cicipi',
                'Tulis 3 hal kecil yang kamu syukuri hari ini dalam jurnal',
                'Gerakkan tubuh — jalan kaki 5 menit atau stretching ringan bisa membantu mengalihkan pikiran',
            ],
            'burnout' => [
                'Istirahat tanpa rasa bersalah — kamu bukan mesin',
                'Kurangi ekspektasi harian: cukup lakukan 1 hal penting hari ini',
                'Batasilah screen time dan konsumsi informasi yang memberatkan pikiran',
            ],
            'pasangan_konflik' => [
                'Ambil jeda 10 menit saat emosi memuncak sebelum melanjutkan diskusi',
                'Gunakan teknik active listening: ulangi apa yang pasangan katakan sebelum merespons',
                'Fokus pada solusi, bukan siapa yang salah',
            ],
            'pasangan_jarak' => [
                'Jadwalkan quality time tanpa distraksi (no handphone) minimal 30 menit',
                'Tanyakan "Apa yang bisa aku lakukan untuk membuatmu merasa lebih dihargai?"',
                'Tulis surat atau pesan kecil yang mengungkapkan perasaanmu secara jujur',
            ],
            'keluarga_tekanan' => [
                'Tetapkan batasan yang sehat (boundary) dengan keluarga secara sopan',
                'Luangkan waktu untuk dirimu sendiri di luar dinamika keluarga',
                'Cari figur suportif di luar keluarga yang bisa diajak bicara',
            ],
            'keluarga_figur' => [
                'Coba lihat perspektif anggota keluarga — mereka juga manusia dengan kelemahan',
                'Tulis harapanmu terhadap hubungan keluarga dan langkah kecil untuk mencapainya',
                'Beranilah memulai obrolan ringan sebagai langkah awal membangun kedekatan',
            ],
            'addiction_recovery' => [
                'Akui dan terima bahwa ada masalah dengan judi online — ini langkah keberanian terbesar',
                'Blokir semua akses ke situs judi, hapus aplikasi top-up, dan nonaktifkan m-banking sementara',
                'Cari satu orang terpercaya (kakak, orang tua, atau konselor) untuk diajak bicara jujur',
            ],
            'theft_dilemma' => [
                'Tenangkan diri dulu — emosi yang campur aduk itu wajar dalam situasi sulit seperti ini',
                'Tulis kronologi kejadian secara objektif — apa yang kamu lihat, dengar, dan rasakan',
                'Cari waktu senggang orang tua, sampaikan dengan I-Message: "Aku melihat kejadiannya, tapi aku belum punya bukti"',
            ],
        ];

        $steps = [];
        foreach ($topics as $topic) {
            foreach ($allSteps as $key => $stepList) {
                $labelMap = [
                    'financial_loss' => 'Kehilangan Materi / Finansial',
                    'fear_parents' => 'Kecemasan Berkomunikasi dengan Orang Tua',
                    'sadness_overthinking' => 'Overthinking & Kesedihan Emosional',
                    'burnout' => 'Kelelahan Mental (Burnout)',
                    'pasangan_konflik' => 'Konflik dalam Hubungan Pasangan',
                    'pasangan_jarak' => 'Jarak Emosional dengan Pasangan',
                    'keluarga_tekanan' => 'Tekanan dari Keluarga',
                    'keluarga_figur' => 'Dinamika Figur Keluarga',
                    'theft_dilemma' => 'Dilema Moral & Tuduhan Tanpa Bukti',
                    'addiction_recovery' => 'Adiksi Judi Online & Krisis Keuangan UKT',
                ];
                if ($topic === ($labelMap[$key] ?? '')) {
                    $steps = array_merge($steps, $stepList);
                }
            }
        }

        if (empty($steps)) {
            $steps = [
                'Luangkan waktu 5 menit untuk menarik napas dalam-dalam dan menenangkan diri',
                'Catat satu hal positif yang terjadi hari ini, sekecil apa pun itu',
                'Ingat bahwa kamu tidak sendirian — Hexa Space selalu ada untukmu',
            ];
        }

        return array_slice($steps, 0, 3);
    }

    private function cleanText(string $text): string
    {
        $text = strtolower($text);
        $text = preg_replace('/[^a-z0-9\s]/', '', $text);
        return trim(preg_replace('/\s+/', ' ', $text));
    }

    private function getLastFiveUserMessages(CounselingSession $session): array
    {
        return ChatMessage::where('counseling_session_id', $session->id)
            ->where('sender', 'user')
            ->latest()
            ->take(5)
            ->pluck('message')
            ->toArray();
    }

    private function getCombinedContext(CounselingSession $session, string $currentCleaned): string
    {
        $recentMessages = ChatMessage::where('counseling_session_id', $session->id)
            ->latest()
            ->take(5)
            ->pluck('message')
            ->toArray();

        $all = $currentCleaned;
        foreach ($recentMessages as $msg) {
            $all .= ' ' . $this->cleanText($msg);
        }
        return $all;
    }

    private function mapIntentToEmotion(string $intentKey): string
    {
        $map = [
            'financial_loss' => 'anxious',
            'fear_parents' => 'anxious',
            'sadness_overthinking' => 'sad',
            'burnout' => 'burnout',
            'pasangan_konflik' => 'sad',
            'pasangan_jarak' => 'sad',
            'keluarga_tekanan' => 'anxious',
            'keluarga_figur' => 'sad',
            'theft_dilemma' => 'anxious',
            'addiction_recovery' => 'anxious',
            'addiction_barrier' => 'anxious',
            'cbt_challenger' => 'sad',
        ];
        return $map[$intentKey] ?? 'neutral';
    }

    private function determineWidget(string $intentKey, string $cleaned, string $combinedHistory): ?string
    {
        $cbtWords = ['tidak bisa', 'menyerah', 'hancur', 'gagal', 'bodoh', 'percuma', 'tidak berguna', 'tidak berharga', 'tidak guna', 'tidak mampu', 'tidak kuat'];
        foreach ($cbtWords as $kw) {
            if (str_contains($cleaned, $kw) || str_contains($combinedHistory, $kw)) return 'cbt_challenger';
        }
        if ($intentKey === 'cbt_challenger') {
            return 'cbt_challenger';
        }

        $panicWords = ['panik', 'cemas', 'sesak', 'takut', 'gelisah', 'degdegan', 'deg deg', 'jantung'];
        foreach ($panicWords as $kw) {
            if (str_contains($cleaned, $kw) || str_contains($combinedHistory, $kw)) return 'box_breathing';
        }

        $addictionWords = ['judi', 'judol', 'gacor', 'xxgacor', 'kecanduan', 'rugi judi', 'slot online', 'deposit judi', 'deposit', 'top up', 'topup', 'uang ukt', 'ukt', 'uang kuliah', 'kalah judi', 'hutang judi', 'utang judi', 'habis buat judi', 'berhenti judi'];
        if ($intentKey === 'addiction_recovery' || $intentKey === 'addiction_barrier') {
            return 'addiction_barrier';
        }
        foreach ($addictionWords as $kw) {
            if (str_contains($cleaned, $kw) || str_contains($combinedHistory, $kw)) return 'addiction_barrier';
        }

        $fearParentWords = ['takut mama', 'takut papa', 'takut orang tua', 'takut dimarahi', 'bilang ke mama', 'ngomong ke mama', 'jujur sama mama', 'jujur sama papa'];
        foreach ($fearParentWords as $kw) {
            if (str_contains($cleaned, $kw) || str_contains($combinedHistory, $kw)) return 'conflict_roadmap';
        }

        $theftWords = ['mencuri', 'mencurinya', 'dituduh', 'tidak ada bukti', 'tanpa bukti', 'melihat teman', 'ketahuan mencuri', 'fitnah'];
        if ($intentKey === 'theft_dilemma') {
            return 'conflict_roadmap';
        }
        foreach ($theftWords as $kw) {
            if (str_contains($cleaned, $kw) || str_contains($combinedHistory, $kw)) return 'conflict_roadmap';
        }

        return null;
    }

    private function checkCrisis(string $cleaned, string $combinedHistory): ?string
    {
        $crisisPhrases = [
            'bunuh diri', 'mati aja', 'akhiri hidup', 'ingin mati',
            'tidak ingin hidup', 'habiskan nyawa', 'potong urat nadi',
            'lompat dari gedung', 'mengakhiri hidup', 'ingin mati',
        ];

        foreach ($crisisPhrases as $phrase) {
            if (str_contains($cleaned, $phrase) || str_contains($combinedHistory, $phrase)) {
                $responses = [
                    'Aku mendengarmu. Sungguh, aku di sini sekarang. Apa yang kamu rasakan pasti sangat berat, dan aku ingin kamu tahu bahwa kamu tidak sendiri. Tolong, jangan menyakiti dirimu sendiri. Segera hubungi hotline krisis Kemenkes di 500-454 atau Into The Light di 0811-123-1123. Ada banyak orang yang peduli dan siap membantumu melewati ini. Kamu berharga.',
                    'Terima kasih sudah jujur dengan perasaanmu yang dalam. Ini adalah langkah yang sangat berani. Namun, kondisi seperti ini perlu ditangani oleh ahlinya. Yuk, hubungi profesional sekarang juga. Kamu bisa menghubungi Hotline Kemenkes 500-454 atau Into The Light 0811-123-1123. Mereka siap mendengarkan dan membantumu. Jangan menyerah.',
                    'Aku turut prihatin dengan apa yang sedang kamu alami. Rasa sakit yang kamu ceritakan itu nyata dan valid. Tapi tolong, jangan ambil keputusan untuk mengakhiri semuanya. Ada harapan dan bantuan nyata di luar sana. Segera telepon Hotline Kemenkes 500-454 atau hubungi Into The Light di 0811-123-1123. Kamu layak mendapatkan pertolongan.',
                ];
                return $responses[array_rand($responses)];
            }
        }

        return null;
    }

    private function getIntents(string $serviceSlug): array
    {
        $globalIntents = [
            'cbt_challenger' => [
                'phrases' => [
                    'tidak bisa', 'menyerah', 'hancur', 'gagal', 'bodoh',
                    'percuma', 'tidak berguna', 'tidak berharga', 'tidak guna',
                    'tidak mampu', 'tidak kuat', 'aku nggak bisa',
                    'nggak berguna', 'nggak berharga', 'semua sia sia',
                    'gagal terus', 'aku bodoh', 'payah', 'gak becus',
                ],
                'responses' => [
                    'Kakak dengar kata-kata yang kamu sampaikan tadi. Kamu bilang kamu merasa gagal, bodoh, atau tidak berguna. Tapi coba lihat dari sudut pandang lain — kamu sedang berjuang dan masih bertahan sampai detik ini. Itu artinya kamu kuat. Pikiran negatif itu datang karena kamu sedang telah, bukan karena kamu benar-benar tidak berguna. Yuk, kita ubah sedikit cara pandangnya. Coba ulangi setelah Kakak: "Aku sedang melalui masa sulit, bukan berarti aku gagal."',
                    'Kakak paham, kadang pikiran kita sendiri yang menjadi musuh terbesar. Kata-kata seperti "aku bodoh" atau "percuma" itu sebenarnya cerminan dari kelelahan, bukan fakta. Coba tantang pikiran itu dengan bertanya: "Apakah benar aku selalu gagal? Atau aku hanya sedang tidak berhasil sekali ini?" Kadang, kita terlalu keras pada diri sendiri. Kamu sudah melakukan yang terbaik dengan sumber daya yang kamu miliki saat itu. Istirahat, lalu coba lagi. Kamu berharga, apa pun hasilnya.',
                    'Perasaan ingin menyerah itu wajar kalau beban yang kamu pikul terasa terlalu berat. Tapi ingat, setiap kali kamu bertahan melewati hari yang sulit, itu adalah bukti bahwa kamu lebih kuat dari yang kamu kira. Coba tulis satu hal kecil yang berhasil kamu lakukan hari ini — sekecil apa pun itu. Mulai dari bangun tidur, minum air, atau sekadar bernapas. Semua itu adalah pencapaian. Kamu tidak perlu sempurna untuk menjadi berarti.',
                ],
            ],
            'addiction_recovery' => [
                'phrases' => [
                    'judi online', 'judol', 'judi', 'slot online',
                    'xxgacor', 'gacor', 'kalah judi', 'rugi judi',
                    'deposit judi', 'deposit', 'top up', 'topup',
                    'uang ukt', 'ukt', 'uang kuliah', 'biaya kuliah',
                    'habis buat judi', 'uang buat judi', 'uang judi',
                    'berhenti judi', 'berhenti main', 'kecanduan judi',
                    'hutang judi', 'utang judi', 'pembayaran ukt',
                    'mau bayar ukt', 'habis 3 juta', 'tabungan habis',
                    'uang sekolah', 'dana pendidikan',
                ],
                'responses' => [
                    'Kakak turut prihatin mendengar kamu mengalami ini. Kecanduan judi online atau kesulitan keuangan karena judi adalah beban yang sangat berat. Perasaan bersalah, malu, dan panik yang kamu alami itu wajar banget. Tapi ingat, ini bukan akhir dari segalanya. Langkah pertama untuk pulih adalah mengakui bahwa ada masalah — dan kamu sudah melakukannya dengan bercerita di sini. Kakak bangga sama kamu. Sekarang, mari kita susun langkah nyata. Ceritakan lebih detail, ya, Kakak di sini untukmu.',
                    'Kakak paham situasi ini pasti terasa menekan banget. Antara rasa bersalah karena uang habis, takut ketahuan keluarga atau teman, dan bingung harus bagaimana ke depannya. Kamu tidak sendiri. Banyak orang pernah berada di posisi ini dan berhasil keluar. Yang penting sekarang adalah kamu mau berubah. Kalau kamu merasa siap, coba rencanakan untuk bicara jujur kepada kakak atau orang tuamu. Contoh kalimatnya: "Kak, aku butuh bantuan. Aku melakukan kesalahan besar karena bermain judi online sampai uang UKT-ku habis. Aku menyesal sekali dan siap menerima konsekuensi serta mencari solusi bersama Kakak." Kamu berani, kok!',
                    'Rasa malu dan bersalah itu berat, tapi jangan biarkan perasaan itu membuatmu mundur. Kamu berharga dan layak untuk pulih. Yuk, kita hadapi pelan-pelan. Mulai dari jujur ke diri sendiri, lalu cari satu orang yang kamu percaya untuk diajak bicara. Kalau kamu masih bingung bagaimana memulainya, coba tulis dulu apa yang ingin kamu sampaikan. Ceritakan ke Kakak apa yang sebenarnya terjadi dan bagaimana perasaanmu sekarang?',
                    'Masalah UKT atau biaya kuliah yang terbengkalai karena judi pasti bikin kamu stres berat. Kakak turut sedih mendengarnya. Tapi kabar baiknya, masih ada jalan keluar. Coba hubungi pihak kampus untuk bicarakan opsi pembayaran atau keringanan UKT. Jangan biarkan rasa malu menghalangimu untuk mencari solusi. Kamu sudah berani cerita di sini, berarti kamu kuat. Coba deh, apa langkah kecil yang bisa kamu lakukan hari ini?',
                ],
            ],
            'theft_dilemma' => [
                'phrases' => [
                    'mencuri', 'mencurinya', 'dituduh', 'menuduh', 'tidak ada bukti',
                    'tanpa bukti', 'melihat teman', 'ketahuan mencuri', 'dituduh mencuri',
                    'dituduh tanpa bukti', 'dituduh padahal tidak', 'fitnah', 'dituduh mencuri',
                    'menuduh tanpa bukti', 'teman mencuri', 'teman saya mencuri',
                    'melihat temanku', 'melihat teman saya',
                ],
                'responses' => [
                    'Kakak turut prihatin dengan situasi pelik yang sedang kamu hadapi. Berada dalam posisi dituduh atau melihat sesuatu yang tidak benar tanpa bisa membuktikan adalah dilema yang sangat berat. Perasaan bingung, takut, dan cemas yang kamu alami itu wajar banget. Kamu manusia, wajar merasa was-was. Coba ceritakan kronologi kejadian secara objektif ke orang tua. Katakan: "Ma, aku melihat kejadiannya langsung, tapi aku belum punya bukti foto atau video. Aku menceritakan ini karena aku ingin jujur dan tidak menyembunyikan apa pun." Dengan begitu, kamu sudah melakukan hal yang benar: jujur tanpa menuduh.',
                    'Di satu sisi kamu ingin jujur kepada orang tua, tapi di sisi lain kamu takut dituduh memfitnah atau merusak hubungan pertemanan karena belum ada bukti. Ini beban moral yang sangat besar untuk seusiamu, dan Kakak bangga kamu sudah mau menceritakannya di sini. Ingat, menyampaikan apa yang kamu lihat secara objektif berbeda dengan menuduh. Fokuslah pada apa yang kamu saksikan, bukan pada asumsi atau kemarahanmu.',
                    'Coba ceritakan kronologi kejadian secara objektif ke orang tua. Katakan: "Ma, aku melihat kejadiannya langsung, tapi aku belum punya bukti foto atau video. Aku menceritakan ini karena aku ingin jujur dan tidak menyembunyikan apa pun." Dengan begitu, kamu sudah melakukan hal yang benar: jujur tanpa menuduh. Kakak yakin orang tua akan menghargai kejujuranmu.',
                    'Langkahmu untuk berdiskusi di Hexa Space sudah sangat tepat. Kamu tidak perlu menyelesaikan semuanya sendirian. Coba bayangkan, apa hal terkecil yang bisa kamu lakukan sekarang untuk meredakan kecemasanmu? Ceritakan lebih detail ya, Kakak di sini untukmu.',
                ],
            ],
            'financial_loss' => [
                'phrases' => [
                    'hilang uang', 'kehilangan uang', 'dompet hilang', 'duit hilang', 'hilang duit',
                    'kecopetan', 'uang hilang', 'kehilangan dompet', 'uang ilang', 'duit ilang',
                    'uang jatuh', 'kehilangan barang', 'barang hilang', 'kemalingan',
                ],
                'responses' => [
                    'Aduh, Kakak turut sedih mendengar kamu mengalami kehilangan. Rasanya pasti panik banget ya apalagi kalau barang itu berharga banget buat kamu. Kamu berhak untuk merasa sedih dan kecewa. Coba tarik napas dulu, kalau sudah lebih tenang, coba ceritakan apa yang sebenarnya terjadi dan bagaimana perasaanmu sekarang?',
                    'Kehilangan barang atau uang itu benar-benar menyakitkan, apalagi jika barang itu memiliki nilai khusus atau memang sedang kamu butuhkan banget. Wajar kalau kamu merasa cemas, kesal, atau bahkan menyalahkan diri sendiri. Tapi ingat ya, ini bukan akhir dari segalanya. Coba ceritakan pelan-pelan, apa yang paling membuatmu berat dari kejadian ini?',
                    'Kakak bisa bayangkan betapa sesak dan paniknya perasaan kamu saat ini. Musibah kayak gini memang datang tiba-tiba dan sering bikin kita merasa bersalah. Tapi kamu sudah sangat berani untuk menceritakan ini. Kadang dengan berbagi, beban terasa lebih ringan. Coba ceritakan lebih detail apa yang hilang atau apa yang kamu rasakan sekarang?',
                ],
            ],
            'fear_parents' => [
                'phrases' => [
                    'ngomong sama mama', 'bilang ke mama', 'takut mama', 'uang punya mama',
                    'jujur ke orang tua', 'takut dimarahi', 'bilang ke orang tua', 'ngomong ke papa',
                    'ngomong ke mama', 'cerita ke mama', 'cerita ke orang tua', 'ngomong sama papa',
                    'bilang ke papa', 'takut papa', 'takut orang tua', 'bicara sama orang tua',
                    'bicara sama mama', 'ngaku ke orang tua', 'jujur sama mama', 'jujur sama papa',
                    'dimarahin ayah', 'dimarahin papa', 'takut ayah', 'takutan mama',
                    'takut sama mama', 'takut sama papa', 'bilang ke ayah', 'ngomong ke ayah',
                ],
                'responses' => [
                    'Kakak paham banget perasaanmu. Ngomong atau jujur sama orang tua itu memang menakutkan, apalagi kalau kita merasa telah mengecewakan mereka. Tapi ingat ya, orang tua biasanya marah karena mereka peduli dan khawatir. Coba cari waktu yang tenang, misalnya setelah makan malam atau saat santai bersama. Mulailah dengan nada yang lembut, akui kesalahanmu dengan tulus, dan sampaikan bahwa kamu ingin bertanggung jawab. Kamu pasti bisa.',
                    'Rasa takut untuk bicara jujur sama mama atau papa itu wajar banget. Mereka pasti ingin yang terbaik buat kamu, meskipun cara mereka menyampaikannya kadang bikin kita ciut. Gimana kalau kita latihan dulu di sini? Coba tulis apa yang ingin kamu sampaikan ke mereka. Kadang, menulis bisa membantu kita merangkai kata dengan lebih tenang. Apa yang paling berat untuk kamu sampaikan?',
                    'Bicara dari hati ke hati dengan orang tua memang butuh keberanian besar. Tapi percayalah, kejujuran adalah langkah pertama menuju pengertian. Coba bayangkan skenario terbaiknya—mungkin mereka akan lebih menghargai kamu karena berani jujur daripada mereka tahu dari orang lain. Kamu berani, kok! Ceritakan dulu ke aku apa yang ingin kamu sampaikan biar kita cari kata-kata yang tepat.',
                ],
            ],
            'sadness_overthinking' => [
                'phrases' => [
                    'sedih', 'nangis', 'kecewa', 'hancur', 'overthinking', 'kesepian', 'kosong',
                    'patah hati', 'hati hancur', 'sendirian', 'gak berharga', 'merasa sendiri',
                    'terluka', 'sakit hati', 'pikiran kacau', 'cemas', 'takut', 'panik',
                    'sepi', 'gelisah', 'kepikiran', 'trauma',
                ],
                'responses' => [
                    'Kakak di sini, ya. Kamu gak sendirian. Air mata yang kamu keluarkan itu valid—artinya kamu manusia yang punya perasaan dalam. Gak apa-apa kok untuk nangis dan merasa hancur. Coba kita tarik napas bareng-bareng dulu, tarik napas panjang… hembuskan perlahan… Nah, kalau sudah sedikit lebih tenang, coba ceritakan apa yang memberatkan hatimu saat ini?',
                    'Aku turut merasakan kesedihan yang kamu alami. Kadang dunia memang terasa berat dan membuat kita overthinking. Kamu berhak untuk merasa kecewa dan sedih. Tapi ingat, ini hanya sementara, tidak selamanya. Coba ceritakan apa yang sedang berkecamuk di kepalamu sekarang—aku akan mendengarkan dengan saksama.',
                    'Rasanya pasti berat ketika semuanya terasa hampa dan sepi. Tapi kamu sudah melakukan hal yang luar biasa dengan memilih untuk bercerita di sini. Itu artinya di dalam dirimu masih ada kekuatan untuk pulih. Peluk dulu dirimu sendiri, lalu ceritakan apa yang paling mengganjal di hatimu saat ini.',
                ],
            ],
            'burnout' => [
                'phrases' => [
                    'capek', 'lelah', 'stres', 'pusing', 'jenuh', 'pasrah', 'menyerah',
                    'capek hati', 'capek banget', 'lelah banget', 'lelah hati', 'pusing mikirin',
                    'stress', 'jenuh banget', 'udah gak kuat', 'gak kuat lagi', 'penat',
                    'ingin berhenti', 'tugas', 'tugas kuliah', 'deadline', 'ujian',
                ],
                'responses' => [
                    'Dengar ya, kamu boleh beristirahat. Kamu bukan mesin yang harus terus berjalan tanpa henti. Lelah secara fisik dan mental itu sinyal kalau tubuh dan pikiranmu butuh jeda. Coba tinggalkan sejenak apa yang memberatkanmu, ambil minum, atau sekadar memejamkan mata. Kalau sudah lebih lega, ceritakan apa yang paling menguras energimu hari ini?',
                    'Kakak dengar kamu sedang sangat lelah. Hidup memang kadang terasa seperti lari marathon tanpa garis finish. Tapi kamu sudah bertahan sejauh ini, dan itu hebat banget. Kamu gak perlu selalu kuat setiap saat. Coba ceritakan satu hal kecil yang membuatmu merasa paling lelah hari ini—kita hadapi bareng-bareng.',
                    'Rasa jenuh dan pasrah itu wajar kalau hidup terasa menekan dari berbagai sisi. Kamu gak sendiri, dan kamu gak harus menyelesaikan semuanya sekarang. Coba prioritaskan istirahat dulu. Kadang setelah pikiran segar, solusi datang dengan sendirinya. Sekarang, ceritakan apa yang paling mengganggu pikiranmu?',
                ],
            ],
        ];

        $serviceIntents = [];

        if ($serviceSlug === 'konseling-pasangan') {
            $serviceIntents = [
                'pasangan_konflik' => [
                    'phrases' => [
                        'berantem', 'bertengkar', 'selingkuh', 'egois', 'pisah', 'cerai', 'putus',
                        'toxic', 'pertengkaran', 'ribut', 'berantem terus', 'sering berantem',
                        'tidak cocok', 'beda pendapat', 'saling menyalahkan',
                    ],
                    'responses' => [
                        'Kakak mengerti betapa melelahkannya berada di situasi konflik seperti ini. Bertengkar dengan orang yang kita sayangi pasti menguras emosionalmu. Apakah konflik ini dipicu karena masalah komunikasi yang tersumbat, atau ada hal lain yang belum sempat kalian bicarakan baik-baik? Ceritakan perlahan ya…',
                        'Konflik dalam hubungan memang hal yang wajar, tapi bukan berarti tidak menyakitkan. Coba ingat-ingat, apakah ada pola yang sama yang terus berulang dalam setiap pertengkaran kalian? Kadang, memahami pola bisa membantu kita melihat akar masalahnya. Ceritakan lebih lanjut, ya.',
                        'Rasanya pasti berat kalau harus berdebat terus-menerus dengan pasangan. Ingat, kamu tidak sendiri dalam menghadapi ini. Coba luangkan waktu untuk menenangkan diri dulu. Kalau sudah siap, ceritakan apa yang sebenarnya kamu rasakan dari dalam hati.',
                    ],
                ],
                'pasangan_jarak' => [
                    'phrases' => [
                        'cuek', 'dingin', 'bosan', 'berubah', 'hambar', 'diabaikan', 'komunikasi',
                        'renggang', 'jarak', 'gak kayak dulu', 'beda', 'berubah banget',
                        'kurang perhatian', 'gak dihargai', 'merasa dijauhkan',
                    ],
                    'responses' => [
                        'Rasanya pasti berat saat merasa mulai ada jarak dalam hubungan. Kadang kita bingung harus memulai pembicaraan dari mana. Coba ceritakan, sejak kapan kamu mulai merasakan perubahan ini? Mungkin dengan mengenali kapan semuanya mulai berubah, kamu bisa menemukan jalan keluarnya.',
                        'Membangun komunikasi yang sehat memang butuh usaha dari kedua belah pihak. Kalau kamu merasa ada jarak, mungkin kalian perlu waktu berdua tanpa distraksi untuk saling mendengar. Apa yang paling kamu rindukan dari hubungan kalian dulu?',
                        'Aku bisa bayangkan betapa membingungkannya ketika semuanya terasa berbeda. Kamu berhak untuk didengar. Coba tuliskan apa yang ingin kamu sampaikan, dan kalau sudah siap, sampaikan dengan pelan-pelan. Aku di sini kalau kamu mau latihan dulu.',
                    ],
                ],
            ];
        } elseif ($serviceSlug === 'konseling-keluarga') {
            $serviceIntents = [
                'keluarga_tekanan' => [
                    'phrases' => [
                        'dituntut', 'dibandingin', 'broken', 'dikekang', 'diatur',
                        'dibanding-bandingkan', 'dibanding bandingkan', 'tuntutan', 'dibandingkan',
                        'dibeda bedain', 'gak bebas', 'dikontrol', 'dianak emaskan',
                    ],
                    'responses' => [
                        'Merasa dituntut atau dibanding-bandingkan oleh keluarga sendiri pasti sangat menyakitkan. Kamu berhak menjadi dirimu sendiri tanpa harus memenuhi ekspektasi orang lain. Coba ceritakan, situasi seperti apa yang paling sering membuatmu merasa tertekan?',
                        'Dinamika keluarga memang rumit, dan kadang tekanan datang dari orang-orang terdekat kita. Tapi ingat, perasaanmu valid. Apa yang bisa kita lakukan sekarang untuk membantu hatimu merasa lebih lega?',
                        'Aku turut sedih mendengar kamu mengalami hal ini. Keluarga seharusnya menjadi tempat paling aman, dan ketika itu tidak terjadi, rasanya pasti sangat berat. Ceritakan lebih detail, ya, aku di sini untuk mendengarkan.',
                    ],
                ],
                'keluarga_figur' => [
                    'phrases' => [
                        'orang tua', 'mama', 'papa', 'ayah', 'ibu', 'kakak', 'adik', 'rumah', 'mertua',
                        'nyokap', 'bokap', 'ortu', 'keluarga',
                    ],
                    'responses' => [
                        'Hubungan dengan anggota keluarga bisa jadi sumber kebahagiaan sekaligus tantangan tersendiri. Apa yang paling ingin kamu perbaiki atau sampaikan kepada mereka? Ceritakan pelan-pelan, aku siap mendengar.',
                        'Figur keluarga memang punya peran besar dalam hidup kita. Kadang ada luka lama yang terbawa sampai sekarang, dan itu wajar. Kalau kamu mau, coba ceritakan kenangan atau perasaan yang paling membekas tentang keluargamu.',
                        'Aku paham bahwa pembahasan tentang keluarga bisa terasa sensitif. Kamu tidak perlu menceritakan semuanya sekaligus. Ceritakan apa yang nyaman buat kamu, dan aku akan ada di sini menemanimu.',
                    ],
                ],
            ];
        }

        return array_merge($globalIntents, $serviceIntents);
    }

    private function getFallbackResponse(string $serviceSlug): array
    {
        $fallbacks = [
            'konseling-pasangan' => [
                'Terima kasih sudah bercerita tentang hubunganmu. Setiap hubungan punya dinamika dan tantangannya sendiri. Kakak ingin memahami lebih dalam—bisa ceritakan lebih spesifik tentang apa yang sedang kamu rasakan saat ini terkait pasanganmu?',
                'Aku senang kamu mau membuka hati di sini. Kadang kita perlu waktu untuk menemukan kata yang tepat. Coba ceritakan pelan-pelan, apa momen terakhir yang membuatmu berpikir tentang hubungan ini?',
                'Bercerita tentang hubungan memang tidak selalu mudah. Tapi dengan kamu melakukannya sekarang, itu menunjukkan bahwa kamu peduli. Coba mulai dari mana yang paling nyaman buat kamu, ya.',
            ],
            'konseling-keluarga' => [
                'Keluarga adalah bagian penting dalam hidup kita. Apa pun yang sedang kamu alami terkait mereka, aku di sini untuk mendengarkan. Coba ceritakan apa yang ingin kamu bagi hari ini—dari hal kecil sekalipun.',
                'Terima kasih sudah percaya untuk bercerita tentang keluargamu. Kadang yang kita butuhkan hanyalah seseorang yang mau mendengar tanpa menghakimi. Ceritakan apa yang ada di hatimu saat ini.',
                'Dinamika keluarga memang penuh warna. Kadang indah, kadang berat. Coba ceritakan satu kenangan atau perasaan yang paling membekas tentang keluargamu saat ini.',
            ],
            'konseling-individu' => [
                'Terima kasih sudah mau bertahan dan menceritakan hal ini ke Kakak. Kalimatmu sangat berharga. Boleh ceritakan lebih detail lagi tentang apa yang sedang mengganjal di pikiranmu saat ini agar Kakak bisa memahami posisimu dengan lebih baik?',
                'Kakak senang kamu memilih untuk berbagi hari ini. Setiap cerita yang kamu bawa adalah langkah keberanian. Coba ceritakan apa yang paling kamu rasakan saat ini—kita bahas bersama pelan-pelan.',
                'Kamu sudah membuat kemajuan dengan hadir dan bercerita di sini. Tidak perlu terburu-buru. Mulailah dari mana yang paling nyaman, dan aku akan menemani setiap langkahmu.',
            ],
        ];

        $pool = $fallbacks[$serviceSlug] ?? [
            'Terima kasih sudah berbagi cerita hari ini. Setiap kata yang kamu tulis adalah bentuk keberanian yang patut diapresiasi. Coba ceritakan lebih lanjut, aku siap mendengarkan dengan saksama tanpa menghakimi.',
            'Kakak menerima ceritamu dengan hangat. Kadang kita tidak perlu langsung menemukan jawaban—yang terpenting adalah kamu mau bercerita. Ceritakan apa pun yang sedang kamu rasakan saat ini, ya.',
            'Halo, terima kasih sudah kembali bercerita. Apa pun yang kamu rasakan saat ini, itu valid dan berharga. Coba mulai dari mana yang paling nyaman buat kamu. Aku di sini untuk menemanimu, langkah demi langkah.',
        ];

        $response = $pool[array_rand($pool)];
        $fullText = $response;
        if ($this->ethicalDisclaimer) {
            $fullText .= "\n\n" . $this->ethicalDisclaimer;
        }

        return [
            'text' => $fullText,
            'emotion' => 'neutral',
            'widget_type' => null,
        ];
    }
}
