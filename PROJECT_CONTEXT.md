# PROJECT_CONTEXT.md - HEXA SPACE

## Nama Project

Hexa Space

## Jenis Project

Website konseling digital berbasis Laravel.

## Tujuan Project

Hexa Space dibuat sebagai website konseling digital berbasis AI dummy yang menyediakan ruang aman bagi pengguna untuk bercerita, memahami perasaan, dan mendapatkan dukungan emosional secara sederhana.

Pada tahap awal, fokus utama aplikasi adalah:

1. User dapat membuka landing page.
2. User dapat register dan login.
3. User dapat masuk ke dashboard.
4. User dapat memilih jenis layanan konseling.
5. User dapat membuat sesi konseling.
6. User dapat melakukan chat sederhana dengan AI dummy.
7. User dapat melihat riwayat sesi konseling miliknya sendiri.

Project ini dibuat bertahap agar tidak berantakan dan mudah dipahami oleh tim pemula.

---

## Tech Stack

Gunakan:

- Laravel
- Blade
- PHP
- MySQL / MariaDB
- Eloquent ORM
- Laravel Breeze untuk auth
- Tailwind CSS atau CSS sederhana
- Vanilla JavaScript jika diperlukan
- Git

Jangan gunakan pada tahap awal:

- React
- Vue
- Next.js
- API-only architecture
- Microservice
- WebSocket / realtime chat
- Payment gateway
- Video call
- AI API asli

---

## Status Project Saat Ini

Project sudah sampai tahap awal backend.

Yang sudah dibuat oleh developer:

- Migration database untuk 4 tabel utama.
- Model untuk kebutuhan awal.
- Beberapa controller.
- Beberapa route di `routes/web.php`.

AI Agent wajib memeriksa kondisi kode yang sudah ada sebelum melakukan perubahan.

Jangan membuat ulang file yang sudah ada tanpa alasan.
Jangan menghapus kode lama tanpa izin.
Jangan menjalankan `migrate:fresh` tanpa izin.

---

## Konsep Website

Nama website:

```text
Hexa Space
```

Tagline:

```text
Ruang Nyaman untuk Pulih dan Bertumbuh
```

Hero text:

```text
Lagi capek sama hidup? Yuk, cerita dulu.
```

Deskripsi singkat:

```text
Kadang isi kepala terasa penuh dan hidup jadi melelahkan. Tenang, kamu nggak harus menghadapi semuanya sendiri.

Hexa Space hadir sebagai ruang aman untuk bercerita bersama konselor berbasis AI yang siap menemani kamu kapan saja.
```

Highlight:

```text
Aman • Nyaman • 24 Jam • Rahasia Terjaga
```

---

## Layanan Konseling

Project memiliki 3 layanan utama:

1. Konseling Individu
2. Konseling Pasangan
3. Konseling Keluarga

### Konseling Individu

Deskripsi:

```text
Pendampingan untuk membantu menghadapi masalah pribadi, stres, overthinking, kecemasan, dan pengembangan diri.
```

### Konseling Pasangan

Deskripsi:

```text
Membantu memahami komunikasi, menyelesaikan konflik hubungan, dan membangun hubungan yang lebih sehat bersama pasangan.
```

### Konseling Keluarga

Deskripsi:

```text
Membantu membangun hubungan keluarga yang lebih harmonis, sehat, dan saling memahami antaranggota keluarga.
```

---

## Role User

Tahap awal hanya membutuhkan dua role:

```text
user
admin
```

Default role saat register:

```text
user
```

Untuk tahap awal:

- User biasa dapat menggunakan layanan konseling.
- Admin boleh disiapkan di database, tetapi dashboard admin lengkap belum wajib dibuat.

---

## Alur Utama User

Alur utama aplikasi:

```text
User membuka landing page
User klik Masuk
User login atau register
User masuk dashboard
User membuka halaman layanan konseling
User memilih salah satu layanan
Sistem membuat sesi konseling
User masuk ke halaman chat
User mengirim pesan
Sistem menyimpan pesan user
Sistem membuat balasan dummy AI
Sistem menyimpan balasan AI
User dapat melihat riwayat sesi
User dapat mengakhiri sesi
```

---

## Fitur yang Tidak Dibuat Dulu

Jangan buat fitur berikut pada tahap awal:

- AI API asli
- Chat realtime
- Payment gateway
- Video call
- Booking jadwal kompleks
- Dashboard admin lengkap
- Artikel/blog
- Upload foto profil
- Notifikasi email
- Multi-role kompleks
- Konselor manusia asli

---

## Prinsip Pengembangan

- Kerjakan satu fitur dalam satu waktu.
- Jangan membuat semua fitur sekaligus.
- Jangan membuat fitur di luar permintaan.
- Jangan mengubah file yang tidak berkaitan.
- Jangan menghapus kode lama tanpa izin.
- Jangan mengubah struktur besar project tanpa izin.
- Gunakan kode yang sederhana dan mudah dipahami pemula.
- Setiap perubahan harus bisa dites.
- Setelah coding, jelaskan file yang dibuat/diubah dan cara mengetesnya.

---

## File Acuan Wajib

Sebelum coding, AI Agent wajib membaca:

```text
PROJECT_CONTEXT.md
DATABASE_PLAN.md
FEATURES.md
DESIGN.md
AI_AGENT_RULES.md
CURRENT_PROGRESS.md
```
