# AI_AGENT_RULES.md - HEXA SPACE

## File Acuan Wajib Dibaca

Sebelum coding, AI Agent wajib membaca:

```text
PROJECT_CONTEXT.md
DATABASE_PLAN.md
FEATURES.md
DESIGN.md
AI_AGENT_RULES.md
CURRENT_PROGRESS.md
```

Jangan langsung coding sebelum memahami konteks project.

---

## Prinsip Kerja

AI Agent harus mengikuti prinsip berikut:

1. Kerjakan satu tugas dalam satu waktu.
2. Jangan membuat semua fitur sekaligus.
3. Jangan membuat fitur di luar permintaan.
4. Jangan mengubah file yang tidak berkaitan.
5. Jangan menghapus kode lama tanpa alasan.
6. Jangan mengubah struktur besar project tanpa izin.
7. Jangan membuat desain baru di luar `DESIGN.md`.
8. Jangan mengubah database sembarangan.
9. Jangan membuat ulang file yang sudah ada tanpa memeriksa isinya.
10. Setelah coding, jelaskan file yang dibuat atau diubah.
11. Setelah coding, jelaskan cara mengetesnya.
12. Jika ada error atau konflik, jelaskan penyebab dan pilihan solusinya.

---

## Kondisi Project Saat Ini

Developer sudah membuat sebagian fondasi:

- Migration database untuk 4 tabel utama.
- Model untuk kebutuhan awal.
- 5 controller.
- Beberapa route di `routes/web.php`.

AI Agent harus melakukan review dulu sebelum mengubah kode.

Jangan menimpa file yang sudah ada.
Jangan menghapus controller/model/migration yang sudah dibuat.
Jika perlu mengubah file yang sudah ada, jelaskan alasannya.

---

## Larangan

AI Agent tidak boleh:

- Membuat payment gateway.
- Membuat video call.
- Membuat chat realtime.
- Membuat AI API asli.
- Membuat booking jadwal kompleks.
- Membuat dashboard admin lengkap tanpa diminta.
- Membuat role selain `user` dan `admin` tanpa izin.
- Menghapus tabel database.
- Menjalankan `php artisan migrate:fresh` tanpa izin.
- Mengubah `DESIGN.md` tanpa diminta.
- Membuat desain baru yang tidak sesuai `DESIGN.md`.
- Hardcode password, token, credential, atau konfigurasi penting.
- Menampilkan data sesi milik user lain.
- Mengubah arsitektur menjadi API-only.
- Menggunakan React, Vue, atau Next.js pada tahap awal.

---

## Aturan Database

Nama database disarankan:

```text
hexa_space
```

Gunakan struktur dari:

```text
DATABASE_PLAN.md
```

Jangan mengubah nama tabel sembarangan.

Jangan membuat migration yang bentrok dengan migration yang sudah ada.

Jika perlu membuat migration baru, jelaskan:

1. Alasannya.
2. Tabel yang terdampak.
3. Kolom yang ditambah/diubah.
4. Cara rollback jika diperlukan.

Jangan menjalankan:

```bash
php artisan migrate:fresh
```

tanpa izin.

---

## Aturan Laravel

Gunakan struktur Laravel yang rapi.

Gunakan folder:

```text
app/Models
app/Http/Controllers
routes/web.php
resources/views
database/migrations
database/seeders
```

Gunakan:

- Controller biasa.
- Model Eloquent.
- Route web.
- Blade view.
- Middleware auth.
- Validasi request sederhana.

Jangan membuat struktur terlalu rumit untuk tahap awal.

---

## Aturan Route

Gunakan route yang sederhana.

Route publik:

```text
/
```

Route auth:

```text
/dashboard
/services
/sessions
/sessions/{session}
```

Route chat:

```text
/sessions/{session}/chat
```

Semua route sesi, layanan, dan chat harus dilindungi middleware `auth`.

Jangan membuat route admin terbuka untuk publik.

---

## Aturan Auth dan Role

Role berada di tabel `users`, field:

```text
role
```

Nilai role:

```text
user
admin
```

Default role:

```text
user
```

Tahap awal:

```text
user -> /dashboard
admin -> boleh tetap /dashboard dulu jika dashboard admin belum dibuat
```

Jangan membuat sistem role kompleks sebelum diminta.

---

## Aturan Akses Data

Akses data wajib aman:

1. User hanya boleh melihat sesi miliknya sendiri.
2. User hanya boleh mengirim pesan ke sesi miliknya sendiri.
3. User tidak boleh mengirim pesan ke sesi yang sudah selesai.
4. User tidak boleh melihat chat milik user lain.
5. Data yang ditampilkan di dashboard harus milik user login.

Jika menggunakan route model binding, tetap validasi kepemilikan data.

---

## Aturan Dummy AI

Tahap awal hanya menggunakan dummy AI response.

AI dummy boleh:

- Memberi respons empatik.
- Mengajak user bercerita lebih lanjut.
- Menenangkan user secara umum.
- Menggunakan kata-kata yang ramah dan aman.

AI dummy tidak boleh:

- Mengklaim sebagai psikolog/dokter profesional.
- Memberi diagnosis medis.
- Memberi instruksi berbahaya.
- Memberi klaim “pasti sembuh”.
- Menggantikan bantuan profesional.

Contoh respons umum:

```text
Terima kasih sudah bercerita. Aku paham ini mungkin tidak mudah untuk kamu. Coba ceritakan lebih lanjut, apa yang paling kamu rasakan saat ini?
```

---

## Aturan UI

Ikuti `DESIGN.md`.

Gunakan:

- Blade
- Tailwind CSS atau CSS sederhana
- Bahasa Indonesia
- Card
- Badge status
- Alert validasi
- Empty state jika data kosong
- Layout responsive sederhana

Jangan membuat style baru yang tidak sesuai `DESIGN.md`.

---

## Aturan Validasi

### Layanan Konseling

Validasi:

```text
service_id wajib
service_id harus ada di counseling_services
service harus aktif
```

### Sesi Konseling

Validasi:

```text
user wajib login
service wajib valid
status hanya active atau finished
```

### Chat Message

Validasi:

```text
message wajib
message harus string
message minimal 1 karakter
message maksimal 1000 karakter
```

---

## Aturan Output Setelah Coding

Setelah selesai coding, AI Agent harus menampilkan:

```text
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

## Aturan Git

Setelah satu fitur berhasil dan sudah dites, sarankan commit.

Contoh:

```bash
git add .
git commit -m "feat: add counseling session flow"
```

Jangan commit ketika fitur masih error.

Satu commit sebaiknya mewakili satu fitur atau satu perbaikan kecil.

---

## Prompt Awal yang Disarankan

Gunakan prompt:

```text
Baca PROJECT_CONTEXT.md, DATABASE_PLAN.md, FEATURES.md, DESIGN.md, AI_AGENT_RULES.md, dan CURRENT_PROGRESS.md terlebih dahulu.

Saya ingin melanjutkan project Hexa Space dari kondisi saat ini.

Tolong review dulu file Laravel yang sudah ada, terutama:
- database/migrations
- app/Models
- app/Http/Controllers
- routes/web.php
- resources/views

Jangan langsung coding sebelum menjelaskan temuanmu.

Setelah review, lanjutkan hanya pada fitur yang saya minta.
Jangan membuat fitur di luar permintaan.
Jangan mengubah desain di luar DESIGN.md.
Jangan menjalankan migrate:fresh.
Setelah selesai, jelaskan file yang dibuat/diubah dan cara mengetesnya.
```
