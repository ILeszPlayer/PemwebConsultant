# PLAN_HEXA_SPACE.md

# Hexa Space — Laravel Blade Project Plan

## 1. Identitas Project

Nama website: Hexa Space

Tagline:
“Ruang Nyaman untuk Pulih dan Bertumbuh”

Konsep:
Hexa Space adalah website konseling digital berbasis AI yang menyediakan ruang aman bagi pengguna untuk bercerita, memahami perasaan, dan mendapatkan dukungan emosional secara sederhana, nyaman, dan rahasia.

Project ini dibuat sebagai aplikasi web berbasis Laravel fullstack, menggunakan Laravel Blade sebagai tampilan utama.

---

## 2. Tujuan Utama Project

Tujuan utama Hexa Space pada tahap awal adalah membuat aplikasi yang memungkinkan user:

1. Membuka landing page Hexa Space.
2. Melakukan register dan login.
3. Melihat dashboard setelah login.
4. Melihat pilihan layanan konseling.
5. Memilih salah satu layanan konseling.
6. Membuat sesi konseling.
7. Melakukan chat sederhana dengan AI dummy.
8. Melihat riwayat sesi konseling miliknya sendiri.

Tahap awal ini tidak langsung membuat seluruh fitur besar agar project tetap rapi, realistis, dan mudah dikembangkan.

---

## 3. Stack Teknologi

Gunakan stack berikut:

- Backend: Laravel
- Frontend: Laravel Blade
- Bahasa: PHP
- Database: MySQL
- Styling: CSS custom atau Tailwind/Bootstrap
- JavaScript: Vanilla JavaScript jika diperlukan
- Auth: Laravel Breeze
- AI: Dummy response terlebih dahulu
- Realtime: Tidak digunakan pada tahap awal

Jangan gunakan:

- React
- Vue
- Next.js
- API frontend terpisah
- Microservice
- WebSocket
- Payment gateway
- Video call
- AI API asli pada tahap awal

---

## 4. Role Tim

### Backend Developer

Backend developer bertugas membuat:

- Setup project Laravel
- Setup database
- Laravel Breeze auth
- Migration
- Model
- Relasi model
- Seeder
- Controller
- Route
- Validasi input
- Logic sesi konseling
- Logic chat dummy AI
- Proteksi agar user hanya dapat melihat data miliknya sendiri

### Frontend Developer

Frontend developer bertugas membuat:

- Layout Blade
- Landing page
- Navbar
- Dashboard
- Card layanan konseling
- Halaman riwayat sesi
- Halaman chat
- Styling warna pastel
- Responsive sederhana

Frontend tetap menggunakan Blade, bukan React atau Vue.

---

## 5. Scope Tahap Awal

Fitur yang harus dibuat pada tahap awal:

1. Landing page
2. Login
3. Register
4. Dashboard user
5. Daftar layanan konseling
6. Membuat sesi konseling
7. Chat dummy AI
8. Riwayat sesi konseling
9. Proteksi data user

Fitur yang belum dibuat pada tahap awal:

1. AI asli menggunakan API
2. Realtime chat
3. Payment gateway
4. Video call
5. Admin dashboard lengkap
6. Artikel/blog
7. Rating layanan
8. Notifikasi email
9. Upload foto profil
10. Konselor manusia asli
11. Jadwal konsultasi kompleks

---

## 6. Konten Landing Page

Landing page harus memuat konten berikut.

### Header

Nama website:
Hexa Space

Tagline:
“Ruang Nyaman untuk Pulih dan Bertumbuh”

Button:
Masuk

---

### Hero Section

Judul:
“Lagi capek sama hidup? Yuk, cerita dulu.”

Deskripsi:
Kadang isi kepala terasa penuh dan hidup jadi melelahkan. Tenang, kamu nggak harus menghadapi semuanya sendiri.

Hexa Space hadir sebagai ruang aman untuk bercerita bersama konselor berbasis AI yang siap menemani kamu kapan saja.

Highlight:
Aman • Nyaman • 24 Jam • Rahasia Terjaga

---

### Beranda

Judul:
Selamat Datang di Hexa Space

Isi:
Halo! Selamat datang di Hexa Space, platform konseling digital berbasis AI yang siap menjadi teman cerita dan ruang aman untukmu.

Di sini, kamu bisa berbagi cerita, mengungkapkan perasaan, dan mendapatkan dukungan emosional kapan saja tanpa takut dihakimi.

---

### Apa Itu Konseling?

Isi:
Konseling adalah proses pendampingan untuk membantu seseorang memahami diri sendiri, mengelola emosi, dan menemukan solusi dari masalah yang sedang dihadapi.

Melalui Hexa Space, proses konseling menjadi lebih mudah diakses, nyaman, dan fleksibel dengan bantuan teknologi AI.

---

### Kenapa Harus Menggunakan Hexa Space?

Isi:

- Bisa bercerita kapan saja selama 24 jam
- Privasi dan kerahasiaan lebih terjaga
- Respon cepat dan mudah digunakan
- Bisa diakses di mana saja
- Membantu mengurangi stres dan overthinking
- Menjadi ruang aman tanpa rasa takut dihakimi

---

### Bagaimana Hexa Space Membantu Klien?

Isi:
Hexa Space membantu pengguna mengekspresikan perasaan, memahami kondisi emosional, dan mendapatkan dukungan saat menghadapi berbagai masalah seperti stres, kecemasan, konflik hubungan, tekanan tugas, maupun rasa lelah secara mental.

Konselor berbasis AI akan memberikan respon yang suportif, membantu pengguna merasa lebih tenang, dan menemani proses refleksi diri dengan nyaman.

---

### Jenis Layanan Konseling

Judul:
Jenis Layanan Konseling

Deskripsi:
Silakan pilih layanan konseling yang sesuai dengan kebutuhanmu.

Hexa Space siap menjadi ruang aman untuk menemani proses pulih dan bertumbuhmu.

Layanan:

1. Konseling Individu
2. Konseling Pasangan
3. Konseling Keluarga

Catatan:
Jangan tampilkan bagian “Pelajari Lebih Lanjut”.
Jangan tampilkan bagian angka statistik seperti 5K+, 98%, atau 24/7.

---

## 7. Tema Desain

Gunakan konsep desain:

- Lembut
- Aman
- Nyaman
- Bersih
- Modern
- Pastel
- Tidak terlalu ramai

Warna utama:

- Ungu pastel
- Pink pastel
- Putih
- Lavender
- Abu gelap untuk teks

Rekomendasi warna:

```txt
Background: #F8F3FF
Primary: #C084FC
Primary Dark: #7E22CE
Secondary: #FBCFE8
Lavender: #E9D5FF
White: #FFFFFF
Text Dark: #374151
Text Soft: #6B7280