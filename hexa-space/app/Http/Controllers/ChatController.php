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

        $crisisKeywords = ['bunuh diri', 'mati aja', 'akhiri hidup', 'ingin mati', 'tidak ingin hidup', 'habiskan nyawa'];
        foreach ($crisisKeywords as $keyword) {
            if (str_contains($cleaned, $keyword)) {
                $crisisResponses = [
                    '… Aku mendengarmu. Sungguh, aku di sini sekarang. Apa yang kamu rasakan pasti sangat berat, dan aku ingin kamu tahu bahwa kamu tidak sendiri. Tolong, jangan menyakiti dirimu sendiri. Segera hubungi hotline krisis Kemenkes di 119 ext 8 atau Into The Light di 0811-123-1123. Ada banyak orang yang peduli dan siap membantumu melewati ini. Kamu berharga.',
                    'Terima kasih sudah jujur dengan perasaanmu yang dalam. Ini adalah langkah yang sangat berani. Namun, kondisi seperti ini perlu ditangani oleh ahlinya. Yuk, hubungi profesional sekarang juga. Kamu bisa menghubungi hotline Kemenkes 119 ext 8 atau Into The Light 0811-123-1123. Mereka siap mendengarkan dan membantumu. Jangan menyerah.',
                    'Aku turut prihatin dengan apa yang sedang kamu alami. Rasa sakit yang kamu ceritakan itu nyata dan valid. Tapi tolong, jangan ambil keputusan untuk mengakhiri semuanya. Ada harapan dan bantuan nyata di luar sana. Segera telepon 119 ext 8 (Kemenkes) atau hubungi Into The Light di 0811-123-1123. Kamu layak mendapatkan pertolongan.',
                ];
                return $crisisResponses[array_rand($crisisResponses)];
            }
        }

        if ($serviceSlug === 'konseling-pasangan') {
            $konflikKeywords = ['berantem', 'bertengkar', 'selingkuh', 'egois', 'pisah', 'cerai', 'putus', 'toxic'];
            foreach ($konflikKeywords as $keyword) {
                if (str_contains($cleaned, $keyword)) {
                    $responses = [
                        'Kakak mengerti betapa melelahkannya berada di situasi konflik seperti ini. Bertengkar dengan orang yang kita sayangi pasti menguras emosionalmu. Apakah konflik ini dipicu karena masalah komunikasi yang tersumbat, atau ada hal lain yang belum sempat kalian bicarakan baik-baik? Ceritakan perlahan ya…',
                        'Konflik dalam hubungan memang hal yang wajar, tapi bukan berarti tidak menyakitkan. Coba ingat-ingat, apakah ada pola yang sama yang terus berulang dalam setiap pertengkaran kalian? Kadang, memahami pola bisa membantu kita melihat akar masalahnya. Ceritakan lebih lanjut, ya.',
                        'Rasanya pasti berat kalau harus berdebat terus-menerus dengan pasangan. Ingat, kamu tidak sendiri dalam menghadapi ini. Coba luangkan waktu untuk menenangkan diri dulu. Kalau sudah siap, ceritakan apa yang sebenarnya kamu rasakan dari dalam hati.',
                    ];
                    return $responses[array_rand($responses)];
                }
            }

            $jarakKeywords = ['cuek', 'dingin', 'bosan', 'berubah', 'hambar', 'diabaikan', 'komunikasi', 'renggang'];
            foreach ($jarakKeywords as $keyword) {
                if (str_contains($cleaned, $keyword)) {
                    $responses = [
                        'Rasanya pasti berat saat merasa mulai ada jarak dalam hubungan. Kadang kita bingung harus memulai pembicaraan dari mana. Coba ceritakan, sejak kapan kamu mulai merasakan perubahan ini? Mungkin dengan mengenali kapan semuanya mulai berubah, kamu bisa menemukan jalan keluarnya.',
                        'Membangun komunikasi yang sehat memang butuh usaha dari kedua belah pihak. Kalau kamu merasa ada jarak, mungkin kalian perlu waktu berdua tanpa distraksi untuk saling mendengar. Apa yang paling kamu rindukan dari hubungan kalian dulu?',
                        'Aku bisa bayangkan betapa membingungkannya ketika semuanya terasa berbeda. Kamu berhak untuk didengar. Coba tuliskan apa yang ingin kamu sampaikan, dan kalau sudah siap, sampaikan dengan pelan-pelan. Aku di sini kalau kamu mau latihan dulu.',
                    ];
                    return $responses[array_rand($responses)];
                }
            }

            $defaultResponses = [
                'Terima kasih sudah bercerita tentang hubunganmu. Setiap hubungan punya dinamika dan tantangannya sendiri. Coba ceritakan lebih spesifik, apa yang sedang kamu rasakan saat ini tentang pasanganmu?',
                'Aku senang kamu mau membuka hati di sini. Hubungan yang sehat dibangun di atas komunikasi, kepercayaan, dan saling pengertian. Bagaimana menurutmu, dari tiga hal itu, mana yang paling perlu kalian perkuat saat ini?',
                'Bercerita tentang hubungan memang tidak selalu mudah. Tapi dengan kamu melakukannya sekarang, itu menunjukkan bahwa kamu peduli dan ingin memperbaiki keadaan. Ceritakan apa yang ada di pikiranmu saat ini, ya.',
            ];
            return $defaultResponses[array_rand($defaultResponses)];
        }

        if ($serviceSlug === 'konseling-keluarga') {
            $tekananKeywords = ['dituntut', 'dibandingin', 'broken', 'dikekang', 'diatur', 'dibanding-bandingkan', 'tuntutan'];
            foreach ($tekananKeywords as $keyword) {
                if (str_contains($cleaned, $keyword)) {
                    $responses = [
                        'Merasa dituntut atau dibanding-bandingkan oleh keluarga sendiri pasti sangat menyakitkan. Kamu berhak menjadi dirimu sendiri tanpa harus memenuhi ekspektasi orang lain. Coba ceritakan, situasi seperti apa yang paling sering membuatmu merasa tertekan?',
                        'Dinamika keluarga memang rumit, dan kadang tekanan datang dari orang-orang terdekat kita. Tapi ingat, perasaanmu valid. Apa yang bisa kita lakukan sekarang untuk membantu hatimu merasa lebih lega?',
                        'Aku turut sedih mendengar kamu mengalami hal ini. Keluarga seharusnya menjadi tempat paling aman, dan ketika itu tidak terjadi, rasanya pasti sangat berat. Ceritakan lebih detail, ya, aku di sini untuk mendengarkan.',
                    ];
                    return $responses[array_rand($responses)];
                }
            }

            $figurKeywords = ['orang tua', 'mama', 'papa', 'ayah', 'ibu', 'kakak', 'adik', 'rumah', 'mertua'];
            foreach ($figurKeywords as $keyword) {
                if (str_contains($cleaned, $keyword)) {
                    $responses = [
                        'Hubungan dengan anggota keluarga bisa jadi sumber kebahagiaan sekaligus tantangan tersendiri. Apa yang paling ingin kamu perbaiki atau sampaikan kepada mereka? Ceritakan pelan-pelan, aku siap mendengar.',
                        'Figur keluarga memang punya peran besar dalam hidup kita. Kadang ada luka lama yang terbawa sampai sekarang, dan itu wajar. Kalau kamu mau, coba ceritakan kenangan atau perasaan yang paling membekas tentang keluargamu.',
                        'Aku paham bahwa pembahasan tentang keluarga bisa terasa sensitif. Kamu tidak perlu menceritakan semuanya sekaligus. Ceritakan apa yang nyaman buat kamu, dan aku akan ada di sini menemanimu.',
                    ];
                    return $responses[array_rand($responses)];
                }
            }

            $defaultResponses = [
                'Keluarga adalah bagian penting dalam hidup kita. Apa pun yang sedang kamu alami terkait mereka, aku di sini untuk mendengarkan. Coba ceritakan apa yang ingin kamu bagi hari ini.',
                'Terima kasih sudah percaya untuk bercerita tentang keluargamu. Setiap keluarga punya cerita masing-masing. Bagaimana perasaanmu saat ini tentang situasi di rumah?',
                'Dinamika keluarga memang penuh warna. Kadang indah, kadang berat. Apa yang paling ingin kamu ceritakan tentang keluargamu saat ini?',
            ];
            return $defaultResponses[array_rand($defaultResponses)];
        }

        $anxietyKeywords = ['sedih', 'cemas', 'nangis', 'takut', 'panik', 'sepi', 'sendiri', 'kosong', 'trauma'];
        foreach ($anxietyKeywords as $keyword) {
            if (str_contains($cleaned, $keyword)) {
                $responses = [
                    'Aku turut merasakan apa yang kamu alami. Perasaan sedih dan cemas itu wajar, dan tidak ada yang salah dengan dirimu karena merasakannya. Coba tarik napas dalam-dalam, dan ceritakan apa yang paling memberatkan hatimu saat ini. Pelan-pelan saja, tidak usah terburu-buru.',
                    'Rasa cemas bisa datang kapan saja dan sering kali sulit dikendalikan. Tapi kamu sudah melakukan hal yang benar dengan mau bercerita. Coba kita bedah sedikit: dari skala 1 sampai 10, seberapa besar rasa cemas yang kamu rasakan sekarang? Ceritakan apa yang memicunya, ya.',
                    'Aku dengar kamu sedang merasa kesepian atau kosong. Perasaan itu memang berat, tapi ingat bahwa kamu tidak sendiri. Banyak orang yang peduli padamu, termasuk aku. Coba ceritakan, kegiatan apa yang biasanya bisa membuatmu merasa sedikit lebih baik?',
                ];
                return $responses[array_rand($responses)];
            }
        }

        $burnoutKeywords = ['capek', 'lelah', 'jenuh', 'pasrah', 'overthinking', 'stres', 'gagal', 'menyerah', 'overthink'];
        foreach ($burnoutKeywords as $keyword) {
            if (str_contains($cleaned, $keyword)) {
                $responses = [
                    'Halo, aku dengar kamu merasa lelah dan itu sangat manusiawi. Tubuh dan pikiranmu butuh istirahat, dan tidak apa-apa untuk mengambil jeda. Coba luangkan waktu sejenak untuk minum air atau sekadar meregangkan badan. Kalau ada yang mau diceritakan, aku di sini.',
                    'Rasa jenuh dan stres bisa menumpuk kalau kita terlalu keras pada diri sendiri. Ingat, kamu sudah bertahan sejauh ini dan itu hebat. Apa yang bisa membuatmu merasa sedikit lebih ringan hari ini? Ceritakan saja, tidak perlu dipikirkan terlalu dalam.',
                    'Aku paham rasa capek yang kamu maksud. Kadang kepala terasa penuh dan semua terasa berat. Kamu tidak perlu menyelesaikan semuanya sekarang. Coba ceritakan satu hal kecil yang mengganggumu hari ini, kita hadapi bersama-sama.',
                ];
                return $responses[array_rand($responses)];
            }
        }

        $defaultResponses = [
            'Terima kasih sudah berbagi cerita. Aku di sini untuk mendengarkan apa pun yang ingin kamu sampaikan. Ceritakan lebih lanjut, aku siap mendengarkan dengan saksama. Ingat, kamu tidak sendiri.',
            'Aku senang kamu mau berbicara hari ini. Setiap cerita yang kamu bagi adalah langkah berani. Coba ceritakan apa yang paling kamu rasakan saat ini, baik itu tentang hari-harimu atau hal lain yang ada di pikiranmu.',
            'Kamu sudah membuat kemajuan dengan hadir dan bercerita di sini. Tidak perlu terburu-buru, ceritakan apa pun yang ada di hatimu. Aku akan mendengarkan tanpa menghakimi. Bagaimana harimu hari ini?',
            'Halo, terima kasih sudah kembali bercerita. Apa pun yang kamu rasakan saat ini, itu valid. Coba mulai dari mana yang paling nyaman buat kamu. Aku di sini untuk menemanimu, langkah demi langkah.',
        ];
        return $defaultResponses[array_rand($defaultResponses)];
    }
}
