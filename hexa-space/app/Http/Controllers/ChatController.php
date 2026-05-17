<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CounselingSession;
use App\Models\ChatMessage;

class ChatController extends Controller
{
    public function store(Request $request, CounselingSession $session)
    {
        if ($session->user_id !== auth()->id()) {
            abort(403, 'Kamu tidak memiliki akses ke sesi ini.');
        }

        if ($session->status === 'finished') {
            return back()->with('error', 'Sesi ini sudah berakhir. Tidak dapat mengirim pesan baru.');
        }

        $request->validate([
            'message' => 'required|string|min:1|max:1000',
        ]);

        ChatMessage::create([
            'counseling_session_id' => $session->id,
            'user_id' => auth()->id(),
            'sender' => 'user',
            'message' => $request->message,
        ]);

        $aiResponse = $this->generateAiResponse($request->message, $session);

        ChatMessage::create([
            'counseling_session_id' => $session->id,
            'user_id' => null,
            'sender' => 'ai',
            'message' => $aiResponse,
        ]);

        return redirect()->route('sessions.show', $session);
    }

    private function cleanText(string $text): string
    {
        $text = strtolower($text);
        $text = preg_replace('/[^a-z0-9\s]/', '', $text);
        return trim(preg_replace('/\s+/', ' ', $text));
    }

    private function generateAiResponse(string $originalMessage, CounselingSession $session): string
    {
        $cleaned = $this->cleanText($originalMessage);
        $serviceSlug = $session->counselingService->slug ?? 'konseling-individu';

        $crisisPhrases = ['bunuh diri', 'mati aja', 'akhiri hidup', 'ingin mati', 'tidak ingin hidup', 'habiskan nyawa'];
        foreach ($crisisPhrases as $phrase) {
            if (str_contains($cleaned, $phrase)) {
                $responses = [
                    '… Aku mendengarmu. Sungguh, aku di sini sekarang. Apa yang kamu rasakan pasti sangat berat, dan aku ingin kamu tahu bahwa kamu tidak sendiri. Tolong, jangan menyakiti dirimu sendiri. Segera hubungi hotline krisis Kemenkes di 119 ext 8 atau Into The Light di 0811-123-1123. Ada banyak orang yang peduli dan siap membantumu melewati ini. Kamu berharga.',
                    'Terima kasih sudah jujur dengan perasaanmu yang dalam. Ini adalah langkah yang sangat berani. Namun, kondisi seperti ini perlu ditangani oleh ahlinya. Yuk, hubungi profesional sekarang juga. Kamu bisa menghubungi hotline Kemenkes 119 ext 8 atau Into The Light 0811-123-1123. Mereka siap mendengarkan dan membantumu. Jangan menyerah.',
                    'Aku turut prihatin dengan apa yang sedang kamu alami. Rasa sakit yang kamu ceritakan itu nyata dan valid. Tapi tolong, jangan ambil keputusan untuk mengakhiri semuanya. Ada harapan dan bantuan nyata di luar sana. Segera telepon 119 ext 8 (Kemenkes) atau hubungi Into The Light di 0811-123-1123. Kamu layak mendapatkan pertolongan.',
                ];
                return $responses[array_rand($responses)];
            }
        }

        $intents = [
            'financial_loss' => [
                'phrases' => [
                    'hilang uang', 'kehilangan uang', 'dompet hilang', 'duit hilang', 'hilang duit',
                    'kecopetan', 'uang hilang', 'kehilangan dompet', 'uang ilang', 'duit ilang',
                    'uang jatuh', 'kehilangan barang', 'barang hilang',
                ],
                'responses' => [
                    'Aduh, Kakak turut sedih mendengar kamu mengalami kehilangan. Rasanya pasti panik banget ya apalagi kalau barang itu berharga banget buat kamu. Kamu berhak untuk merasa sedih dan kecewa. Coba tarik napas dulu, kalau sudah lebih tenang, coba ceritakan apa yang sebenarnya terjadi dan bagaimana perasaanmu sekarang?',
                    'Kehilangan barang atau uang itu benar-benar menyakitkan, apalagi jika barang itu memiliki nilai khusus atau memang sedang kamu butuhkan banget. Wajar kalau kamu merasa cemas, kesal, atau bahkan menyalahkan diri sendiri. Tapi ingat ya, ini bukan akhir dari segalanya. Coba ceritakan pelan-pelan, apa yang paling membuatmu berat dari kejadian ini?',
                    'Kakak bisa bayangkan betapa sesak dan paniknya perasaan kamu saat ini. Musibah kayak gini memang datang tiba-tiba dan sering bikin kita merasa bersalah. Tapi kamu sudah sangat berani untuk menceritakan ini. Kadang dengan berbagi, beban terasa lebih ringan. Coba ceritakan lebih detail apa yang hilang atau apa yang kamu rasakan sekarang?',
                ],
                'service_filter' => null,
            ],
            'fear_parents' => [
                'phrases' => [
                    'ngomong sama mama', 'bilang ke mama', 'takut mama', 'uang punya mama',
                    'jujur ke orang tua', 'takut dimarahi', 'bilang ke orang tua', 'ngomong ke papa',
                    'ngomong ke mama', 'cerita ke mama', 'cerita ke orang tua', 'ngomong sama papa',
                    'bilang ke papa', 'takut papa', 'takut orang tua', 'bicara sama orang tua',
                    'bicara sama mama', 'ngaku ke orang tua', 'jujur sama mama',
                ],
                'responses' => [
                    'Kakak paham banget perasaanmu. Ngomong atau jujur sama orang tua itu memang menakutkan, apalagi kalau kita merasa telah mengecewakan mereka. Tapi ingat ya, orang tua biasanya marah karena mereka peduli dan khawatir. Coba cari waktu yang tenang, misalnya setelah makan malam atau saat santai bersama. Mulailah dengan nada yang lembut, akui kesalahanmu dengan tulus, dan sampaikan bahwa kamu ingin bertanggung jawab. Kamu pasti bisa.',
                    'Rasa takut untuk bicara jujur sama mama atau papa itu wajar banget. Mereka pasti ingin yang terbaik buat kamu, meskipun cara mereka menyampaikannya kadang bikin kita ciut. How about kita latihan dulu di sini? Coba tulis apa yang ingin kamu sampaikan ke mereka. Kadang, menulis bisa membantu kita merangkai kata dengan lebih tenang.',
                    'Bicara dari hati ke hati dengan orang tua memang butuh keberanian besar. Tapi percayalah, kejujuran adalah langkah pertama menuju pengertian. Coba bayangkan skenario terbaiknya—mungkin mereka akan lebih menghargai kamu karena berani jujur daripada mereka tahu dari orang lain. Kamu berani, kok! Ceritakan dulu ke aku apa yang ingin kamu sampaikan biar kita cari kata-kata yang tepat.',
                ],
                'service_filter' => null,
            ],
            'sadness_overthinking' => [
                'phrases' => [
                    'sedih', 'nangis', 'kecewa', 'hancur', 'overthinking', 'kesepian', 'kosong',
                    'patah hati', 'hati hancur', 'sendirian', 'gak berharga', 'merasa sendiri',
                    'terluka', 'sakit hati', 'pikiran kacau',
                ],
                'responses' => [
                    'Kakak di sini, ya. Kamu gak sendirian. Air mata yang kamu keluarkan itu valid—artinya kamu manusia yang punya perasaan dalam. Gak apa-apa kok untuk nangis dan merasa hancur. Coba kita tarik napas bareng-bareng dulu, tarik napas panjang… hembuskan perlahan… Nah, kalau sudah sedikit lebih tenang, coba ceritakan apa yang memberatkan hatimu saat ini?',
                    'Aku turut merasakan kesedihan yang kamu alami. Kadang dunia memang terasa berat dan membuat kita overthinking. Kamu berhak untuk merasa kecewa dan sedih. Tapi ingat, ini hanya sementara, tidak selamanya. Coba ceritakan apa yang sedang berkecamuk di kepalamu sekarang—aku akan mendengarkan dengan saksama.',
                    'Rasanya pasti berat ketika semuanya terasa hampa dan sepi. Tapi kamu sudah melakukan hal yang luar biasa dengan memilih untuk bercerita di sini. Itu artinya di dalam dirimu masih ada kekuatan untuk pulih. Peluk dulu dirimu sendiri, lalu ceritakan apa yang paling mengganjal di hatimu saat ini.',
                ],
                'service_filter' => null,
            ],
            'burnout' => [
                'phrases' => [
                    'capek', 'lelah', 'stres', 'pusing', 'jenuh', 'pasrah', 'menyerah',
                    'capek hati', 'capek banget', 'lelah banget', 'lelah hati', 'pusing mikirin',
                    'stress', 'jenuh banget', 'udah gak kuat', 'gak kuat lagi',
                ],
                'responses' => [
                    'Dengar ya, kamu boleh beristirahat. Kamu bukan mesin yang harus terus berjalan tanpa henti. Lelah secara fisik dan mental itu sinyal kalau tubuh dan pikiranmu butuh jeda. Coba tinggalkan sejenak apa yang memberatkanmu, ambil minum, atau sekadar memejamkan mata. Kalau sudah lebih lega, ceritakan apa yang paling menguras energimu hari ini?',
                    'Kakak dengar kamu sedang sangat lelah. Hidup memang kadang terasa seperti lari marathon tanpa garis finish. Tapi kamu sudah bertahan sejauh ini, dan itu hebat banget. Kamu gak perlu selalu kuat setiap saat. Coba ceritakan satu hal kecil yang membuatmu merasa paling lelah hari ini—kita hadapi bareng-bareng.',
                    'Rasa jenuh dan pasrah itu wajar kalau hidup terasa menekan dari berbagai sisi. Kamu gak sendiri, dan kamu gak harus menyelesaikan semuanya sekarang. Coba prioritaskan istirahat dulu. Kadang setelah pikiran segar, solusi datang dengan sendirinya. Sekarang, ceritakan apa yang paling mengganggu pikiranmu?',
                ],
                'service_filter' => null,
            ],
        ];

        $serviceSpecificIntents = [];

        if ($serviceSlug === 'konseling-pasangan') {
            $serviceSpecificIntents = [
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
            $serviceSpecificIntents = [
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

        $allIntents = array_merge($intents, $serviceSpecificIntents);

        $scoredIntents = [];
        foreach ($allIntents as $key => $intent) {
            $score = 0;
            foreach ($intent['phrases'] as $phrase) {
                if (str_contains($cleaned, $phrase)) {
                    $score++;
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
            return $best['responses'][array_rand($best['responses'])];
        }

        if ($serviceSlug === 'konseling-pasangan') {
            $fallback = [
                'Terima kasih sudah bercerita tentang hubunganmu. Setiap hubungan punya dinamika dan tantangannya sendiri. Kakak ingin memahami lebih dalam—bisa ceritakan lebih spesifik tentang apa yang sedang kamu rasakan saat ini terkait pasanganmu?',
                'Aku senang kamu mau membuka hati di sini. Kadang kita perlu waktu untuk menemukan kata yang tepat. Coba ceritakan pelan-pelan, apa momen terakhir yang membuatmu berpikir tentang hubungan ini?',
                'Bercerita tentang hubungan memang tidak selalu mudah. Tapi dengan kamu melakukannya sekarang, itu menunjukkan bahwa kamu peduli. Coba mulai dari mana yang paling nyaman buat kamu, ya.',
            ];
            return $fallback[array_rand($fallback)];
        }

        if ($serviceSlug === 'konseling-keluarga') {
            $fallback = [
                'Keluarga adalah bagian penting dalam hidup kita. Apa pun yang sedang kamu alami terkait mereka, aku di sini untuk mendengarkan. Coba ceritakan apa yang ingin kamu bagi hari ini—dari hal kecil sekalipun.',
                'Terima kasih sudah percaya untuk bercerita tentang keluargamu. Kadang yang kita butuhkan hanyalah seseorang yang mau mendengar tanpa menghakimi. Ceritakan apa yang ada di hatimu saat ini.',
                'Dinamika keluarga memang penuh warna. Kadang indah, kadang berat. Coba ceritakan satu kenangan atau perasaan yang paling membekas tentang keluargamu saat ini.',
            ];
            return $fallback[array_rand($fallback)];
        }

        if ($serviceSlug === 'konseling-individu') {
            $fallback = [
                'Terima kasih sudah mau bertahan dan menceritakan hal ini ke Kakak. Kalimatmu sangat berharga. Boleh ceritakan lebih detail lagi tentang apa yang sedang mengganjal di pikiranmu saat ini agar Kakak bisa memahami posisimu dengan lebih baik?',
                'Kakak senang kamu memilih untuk berbagi hari ini. Setiap cerita yang kamu bawa adalah langkah keberanian. Coba ceritakan apa yang paling kamu rasakan saat ini—kita bahas bersama pelan-pelan.',
                'Kamu sudah membuat kemajuan dengan hadir dan bercerita di sini. Tidak perlu terburu-buru. Mulailah dari mana yang paling nyaman, dan aku akan menemani setiap langkahmu.',
            ];
            return $fallback[array_rand($fallback)];
        }

        $generalFallback = [
            'Terima kasih sudah berbagi cerita hari ini. Setiap kata yang kamu tulis adalah bentuk keberanian yang patut diapresiasi. Coba ceritakan lebih lanjut, aku siap mendengarkan dengan saksama tanpa menghakimi.',
            'Kakak menerima ceritamu dengan hangat. Kadang kita tidak perlu langsung menemukan jawaban—yang terpenting adalah kamu mau bercerita. Ceritakan apa pun yang sedang kamu rasakan saat ini, ya.',
            'Halo, terima kasih sudah kembali bercerita. Apa pun yang kamu rasakan saat ini, itu valid dan berharga. Coba mulai dari mana yang paling nyaman buat kamu. Aku di sini untuk menemanimu, langkah demi langkah.',
        ];
        return $generalFallback[array_rand($generalFallback)];
    }
}
