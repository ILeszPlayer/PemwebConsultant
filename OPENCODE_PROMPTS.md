# OPENCODE_PROMPTS.md - HEXA SPACE

File ini berisi prompt yang bisa ditempel ke OpenCode.

Gunakan prompt secara bertahap. Jangan menjalankan semua prompt sekaligus.

---

# Prompt 1 - Review Project Saat Ini

Gunakan prompt ini pertama kali karena project sudah punya migration, model, controller, dan route.

```text
Gunakan model GPT-5.5 Pro dengan reasoning mode xhigh.

Baca file berikut terlebih dahulu sampai selesai:

- PROJECT_CONTEXT.md
- DATABASE_PLAN.md
- FEATURES.md
- DESIGN.md
- AI_AGENT_RULES.md
- CURRENT_PROGRESS.md

Saya ingin melanjutkan project Laravel Hexa Space dari kondisi yang sudah ada.

Saat ini saya sudah membuat:
- migration database untuk 4 tabel utama
- model
- 5 controller
- beberapa route di routes/web.php

Tolong lakukan review dulu, jangan langsung coding.

Periksa file berikut:
- database/migrations
- app/Models
- app/Http/Controllers
- routes/web.php
- resources/views

Tugas review:
1. Jelaskan struktur project yang kamu temukan.
2. Jelaskan migration apa saja yang sudah ada.
3. Jelaskan model apa saja yang sudah ada.
4. Jelaskan controller apa saja yang sudah ada.
5. Jelaskan route apa saja yang sudah ada.
6. Bandingkan dengan DATABASE_PLAN.md dan FEATURES.md.
7. Sebutkan bagian yang sudah sesuai.
8. Sebutkan bagian yang kurang atau perlu dirapikan.
9. Jangan mengubah file apa pun dulu.
10. Beri rekomendasi langkah coding berikutnya yang paling aman.

Penting:
- Jangan membuat fitur di luar permintaan.
- Jangan menghapus kode lama.
- Jangan menjalankan migrate:fresh.
- Jangan mengubah desain.
- Jangan memakai React/Vue.
- Tetap gunakan Laravel Blade.
```

---

# Prompt 2 - Rapikan Database, Model, Relasi, dan Seeder

Gunakan setelah prompt review selesai.

```text
Gunakan model GPT-5.5 Pro dengan reasoning mode xhigh.

Baca lagi:
- PROJECT_CONTEXT.md
- DATABASE_PLAN.md
- FEATURES.md
- DESIGN.md
- AI_AGENT_RULES.md
- CURRENT_PROGRESS.md

Lanjutkan project Hexa Space hanya untuk merapikan fondasi database, model, relasi, dan seeder.

Scope pekerjaan:
1. Cek migration yang sudah ada.
2. Jika ada field penting yang kurang, buat migration tambahan yang aman.
3. Jangan menghapus migration lama.
4. Jangan menjalankan migrate:fresh.
5. Pastikan users memiliki role default user.
6. Pastikan counseling_services sesuai DATABASE_PLAN.md.
7. Pastikan counseling_sessions sesuai DATABASE_PLAN.md.
8. Pastikan chat_messages sesuai DATABASE_PLAN.md.
9. Pastikan model memiliki fillable.
10. Pastikan relasi model sudah benar.
11. Buat atau rapikan CounselingServiceSeeder.
12. Seeder harus mengisi:
    - Konseling Individu
    - Konseling Pasangan
    - Konseling Keluarga
13. Seeder harus aman dijalankan ulang, gunakan updateOrCreate.

Jangan mengerjakan:
- UI besar
- chat logic
- AI API asli
- realtime chat
- dashboard admin
- payment
- video call

Setelah selesai, tampilkan:
File dibuat:
- ...

File diubah:
- ...

Fungsi perubahan:
- ...

Command yang perlu saya jalankan:
- ...

Cara tes:
1. ...
2. ...
3. ...

Catatan:
- ...
```

---

# Prompt 3 - Implementasi Controller dan Route Inti

Gunakan setelah database/model/seeder aman.

```text
Gunakan model GPT-5.5 Pro dengan reasoning mode xhigh.

Baca lagi semua file acuan:
- PROJECT_CONTEXT.md
- DATABASE_PLAN.md
- FEATURES.md
- DESIGN.md
- AI_AGENT_RULES.md
- CURRENT_PROGRESS.md

Lanjutkan hanya untuk implementasi controller dan route inti Hexa Space.

Scope pekerjaan:
1. Rapikan atau lengkapi HomeController.
2. Rapikan atau lengkapi DashboardController jika sudah ada.
3. Rapikan atau lengkapi CounselingServiceController.
4. Rapikan atau lengkapi CounselingSessionController.
5. Rapikan atau lengkapi ChatController.
6. Rapikan route di routes/web.php.
7. Gunakan middleware auth untuk route dashboard, services, sessions, dan chat.
8. Pastikan user hanya bisa mengakses sesi miliknya sendiri.
9. Pastikan user tidak bisa chat di sesi yang sudah finished.
10. Validasi input chat:
    - required
    - string
    - min 1
    - max 1000
11. Buat dummy AI response sederhana.
12. Jangan membuat AI API asli.

Route yang diharapkan:
- GET /
- GET /dashboard
- GET /services
- GET /sessions
- POST /sessions
- GET /sessions/{session}
- PATCH /sessions/{session}/finish
- POST /sessions/{session}/chat

Jangan mengerjakan:
- desain detail
- realtime chat
- payment
- video call
- admin dashboard
- fitur artikel

Setelah selesai, tampilkan:
File dibuat:
- ...

File diubah:
- ...

Fungsi perubahan:
- ...

Route yang tersedia:
- ...

Cara tes:
1. ...
2. ...
3. ...

Catatan:
- ...
```

---

# Prompt 4 - Buat Blade Sederhana untuk Testing Alur

Gunakan setelah controller dan route inti aman.

```text
Gunakan model GPT-5.5 Pro dengan reasoning mode xhigh.

Baca lagi semua file acuan:
- PROJECT_CONTEXT.md
- DATABASE_PLAN.md
- FEATURES.md
- DESIGN.md
- AI_AGENT_RULES.md
- CURRENT_PROGRESS.md

Lanjutkan hanya untuk membuat Blade sederhana agar alur aplikasi bisa dites.

Scope halaman:
1. Landing page
2. Dashboard
3. Daftar layanan konseling
4. Riwayat sesi konseling
5. Detail sesi / chat

Gunakan:
- Laravel Blade
- Tailwind CSS atau CSS sederhana
- Warna pastel sesuai DESIGN.md
- Bahasa Indonesia
- Data dari controller dan database

Landing page wajib memuat:
- Hexa Space
- “Ruang Nyaman untuk Pulih dan Bertumbuh”
- “Lagi capek sama hidup? Yuk, cerita dulu.”
- Deskripsi Hexa Space
- Aman • Nyaman • 24 Jam • Rahasia Terjaga
- Beranda
- Apa Itu Konseling?
- Kenapa Harus Menggunakan Hexa Space?
- Bagaimana Hexa Space Membantu Klien?
- Jenis Layanan Konseling

Jangan tampilkan:
- “Pelajari Lebih Lanjut”
- Statistik angka seperti 5K+, 98%, 24/7 cards
- Desain terlalu kompleks

Setelah selesai, tampilkan:
File dibuat:
- ...

File diubah:
- ...

Fungsi perubahan:
- ...

Cara tes:
1. ...
2. ...
3. ...

Catatan:
- ...
```

---

# Prompt 5 - Testing dan Proteksi MVP

Gunakan setelah alur dasar sudah jalan.

```text
Gunakan model GPT-5.5 Pro dengan reasoning mode xhigh.

Baca semua file acuan:
- PROJECT_CONTEXT.md
- DATABASE_PLAN.md
- FEATURES.md
- DESIGN.md
- AI_AGENT_RULES.md
- CURRENT_PROGRESS.md

Lakukan review dan perbaikan kecil untuk memastikan MVP Hexa Space aman dan berjalan.

Scope:
1. Cek user tidak bisa membuka sesi milik user lain.
2. Cek user tidak bisa mengirim chat ke sesi milik user lain.
3. Cek user tidak bisa mengirim chat jika sesi finished.
4. Cek validasi input chat.
5. Cek route yang wajib auth sudah memakai middleware auth.
6. Cek empty state di halaman dashboard, services, sessions, dan chat.
7. Cek redirect setelah membuat sesi.
8. Cek redirect setelah mengakhiri sesi.
9. Cek pesan error atau success.

Jangan menambahkan fitur besar.

Jangan membuat:
- AI API asli
- realtime chat
- payment
- video call
- admin dashboard
- artikel/blog

Setelah selesai, tampilkan:
File dibuat:
- ...

File diubah:
- ...

Perbaikan keamanan:
- ...

Perbaikan validasi:
- ...

Checklist testing:
1. ...
2. ...
3. ...

Catatan:
- ...
```

---

# Prompt Cepat untuk Tiap Fitur Kecil

Gunakan format ini jika ingin meminta satu tugas kecil.

```text
Gunakan model GPT-5.5 Pro dengan reasoning mode xhigh.

Baca PROJECT_CONTEXT.md, DATABASE_PLAN.md, FEATURES.md, DESIGN.md, AI_AGENT_RULES.md, dan CURRENT_PROGRESS.md.

Saya ingin mengerjakan fitur kecil berikut:
[nama fitur]

Batasan:
- Jangan mengerjakan fitur lain.
- Jangan mengubah file yang tidak berkaitan.
- Jangan menghapus kode lama tanpa izin.
- Jangan menjalankan migrate:fresh.
- Ikuti DESIGN.md.
- Gunakan Laravel Blade.

Setelah selesai, jelaskan:
1. File dibuat.
2. File diubah.
3. Fungsi perubahan.
4. Cara tes.
5. Catatan penting.
```
