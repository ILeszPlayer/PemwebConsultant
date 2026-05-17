# FEATURES.md - HEXA SPACE

## Konsep Aplikasi

Hexa Space adalah website konseling digital berbasis Laravel Blade.

Aplikasi tahap awal memiliki:

- Landing page
- Login dan register
- Dashboard user
- Daftar layanan konseling
- Pembuatan sesi konseling
- Chat sederhana dengan AI dummy
- Riwayat sesi konseling
- Fitur akhiri sesi
- Proteksi agar user hanya dapat mengakses sesi miliknya sendiri

Aplikasi tahap awal tidak menggunakan AI API asli.

---

# Phase 1 - Review Project yang Sudah Ada

## Tujuan

AI Agent harus memahami kondisi project saat ini sebelum coding.

## Tugas

- Baca semua file acuan.
- Periksa migration yang sudah ada.
- Periksa model yang sudah ada.
- Periksa controller yang sudah ada.
- Periksa route di `routes/web.php`.
- Jangan membuat ulang file yang sudah ada jika tidak perlu.
- Jangan menghapus kode yang sudah dibuat developer.

## Output

- Ringkasan kondisi project saat ini.
- Daftar file penting yang ditemukan.
- Saran langkah berikutnya.
- Konfirmasi sebelum melakukan perubahan besar.

---

# Phase 2 - Rapikan Database Foundation

## Tujuan

Memastikan struktur database awal sudah benar.

## Fitur

- Tabel users memiliki field `role`.
- Tabel counseling_services tersedia.
- Tabel counseling_sessions tersedia.
- Tabel chat_messages tersedia.
- Relasi foreign key sudah sesuai.
- Seeder layanan konseling tersedia.

## Output

- Migration tidak bentrok.
- Model memiliki fillable yang sesuai.
- Relasi model berjalan.
- Seeder mengisi data layanan konseling.

---

# Phase 3 - Auth dan Dashboard Dasar

## Tujuan

User dapat login, register, dan masuk dashboard.

## Fitur

- Login
- Register
- Logout
- Dashboard user setelah login
- Role default user

## Aturan

- Gunakan Laravel Breeze jika belum ada.
- Jangan membuat sistem auth manual dari nol jika Breeze sudah tersedia.
- Setelah login, user masuk ke `/dashboard`.

---

# Phase 4 - Layanan Konseling

## Tujuan

User dapat melihat dan memilih layanan konseling.

## Fitur

- Tampilkan daftar layanan aktif.
- Tampilkan nama, icon, dan deskripsi layanan.
- Button pilih layanan.
- Saat user memilih layanan, sistem membuat sesi baru.

## Route yang disarankan

```text
GET  /services
POST /sessions
```

## Aturan

- Hanya user login yang bisa membuka layanan.
- Hanya layanan aktif yang bisa dipilih.
- Jika layanan tidak aktif, jangan buat sesi.

---

# Phase 5 - Sesi Konseling

## Tujuan

User dapat melihat riwayat dan detail sesi konseling.

## Fitur

- Daftar riwayat sesi milik user.
- Detail sesi konseling.
- Status sesi: active atau finished.
- User bisa mengakhiri sesi.

## Route yang disarankan

```text
GET   /sessions
GET   /sessions/{session}
PATCH /sessions/{session}/finish
```

## Aturan

- User hanya boleh melihat sesi miliknya sendiri.
- User tidak boleh membuka sesi milik user lain.
- Sesi yang sudah finished tetap bisa dilihat, tapi tidak bisa dikirim pesan baru.

---

# Phase 6 - Chat Dummy AI

## Tujuan

User dapat mengirim pesan dan menerima balasan dummy dari AI.

## Fitur

- Form input pesan.
- Simpan pesan user.
- Generate balasan dummy AI.
- Simpan balasan AI.
- Tampilkan semua pesan berdasarkan urutan waktu.

## Route yang disarankan

```text
POST /sessions/{session}/chat
```

## Validasi

```text
message wajib
message harus string
message minimal 1 karakter
message maksimal 1000 karakter
```

## Aturan

- User hanya boleh chat pada sesi miliknya sendiri.
- User tidak boleh chat pada sesi yang sudah `finished`.
- AI dummy tidak boleh memberi diagnosis medis.
- AI dummy tidak boleh mengklaim sebagai psikolog profesional.
- AI dummy harus memberi respons suportif dan aman.

---

# Phase 7 - Landing Page dan UI Dasar

## Tujuan

Membuat tampilan awal yang sesuai konsep Hexa Space.

## Fitur Landing Page

- Header
- Button Masuk
- Hero section
- Beranda
- Apa Itu Konseling
- Kenapa Harus Menggunakan Hexa Space
- Bagaimana Hexa Space Membantu Klien
- Jenis Layanan Konseling

## Aturan

- Gunakan warna pastel ungu, putih, dan pink.
- Jangan tampilkan bagian “Pelajari Lebih Lanjut”.
- Jangan tampilkan angka statistik seperti 5K+, 98%, atau 24/7 cards.
- Gunakan Blade, bukan React/Vue.

---

# Phase 8 - Proteksi dan Testing

## Tujuan

Merapikan keamanan dasar dan memastikan alur berjalan.

## Checklist

- User bisa register.
- User bisa login.
- User bisa melihat dashboard.
- User bisa melihat layanan.
- User bisa membuat sesi.
- User bisa membuka sesi miliknya.
- User tidak bisa membuka sesi user lain.
- User bisa mengirim chat.
- AI dummy membalas chat.
- Pesan tersimpan di database.
- User bisa melihat riwayat.
- User bisa mengakhiri sesi.
- User tidak bisa chat setelah sesi selesai.
- Validasi input berjalan.

---

# Fitur yang Ditunda

Jangan buat fitur ini sampai MVP stabil:

- AI API asli
- Chat realtime
- Payment gateway
- Video call
- Dashboard admin lengkap
- Artikel/blog
- Upload foto profil
- Notifikasi email
- Konselor manusia
- Jadwal konsultasi kompleks
