<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CounselingService;

class CounselingServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            [
                'name' => 'Konseling Individu',
                'slug' => 'konseling-individu',
                'icon' => '👤',
                'description' => 'Pendampingan untuk membantu menghadapi masalah pribadi, stres, overthinking, kecemasan, dan pengembangan diri.',
                'is_active' => true,
            ],
            [
                'name' => 'Konseling Pasangan',
                'slug' => 'konseling-pasangan',
                'icon' => '💞',
                'description' => 'Membantu memahami komunikasi, menyelesaikan konflik hubungan, dan membangun hubungan yang lebih sehat bersama pasangan.',
                'is_active' => true,
            ],
            [
                'name' => 'Konseling Keluarga',
                'slug' => 'konseling-keluarga',
                'icon' => '👨‍👩‍👧',
                'description' => 'Membantu membangun hubungan keluarga yang lebih harmonis, sehat, dan saling memahami antaranggota keluarga.',
                'is_active' => true,
            ],
        ];

        foreach ($services as $service) {
            CounselingService::updateOrCreate(
                ['slug' => $service['slug']],
                $service
            );
        }
    }
}
