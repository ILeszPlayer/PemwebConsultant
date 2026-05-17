# DATABASE_PLAN.md - HEXA SPACE

## Tujuan Database

Database Hexa Space digunakan untuk menyimpan:

1. Data user.
2. Data layanan konseling.
3. Data sesi konseling.
4. Data pesan chat dalam sesi konseling.

Tahap awal cukup menggunakan struktur sederhana agar aplikasi mudah dikembangkan.

---

## Database

Nama database disarankan:

```text
hexa_space
```

Gunakan MySQL atau MariaDB.

Jangan menjalankan:

```bash
php artisan migrate:fresh
```

tanpa izin dari developer.

Jika perlu reset database, jelaskan dulu alasan dan dampaknya.

---

## Tabel Utama

Gunakan 4 tabel utama:

1. `users`
2. `counseling_services`
3. `counseling_sessions`
4. `chat_messages`

---

## 1. Tabel users

Tabel `users` menggunakan bawaan Laravel Breeze.

Tambahkan field:

```text
role
```

Detail field tambahan:

```text
role: string, default user
```

Nilai role:

```text
user
admin
```

Aturan:

- User baru otomatis memiliki role `user`.
- Role `admin` disiapkan untuk pengembangan berikutnya.
- Tahap awal tidak perlu dashboard admin kompleks.

---

## 2. Tabel counseling_services

Fungsi:

Menyimpan jenis layanan konseling.

Kolom:

```text
id
name
slug
description
icon
is_active
created_at
updated_at
```

Detail:

```text
name        : string, required
slug        : string, unique, required
description : text, required
icon        : string, nullable
is_active   : boolean, default true
```

Data awal:

```text
Konseling Individu
Konseling Pasangan
Konseling Keluarga
```

Contoh data:

```text
name: Konseling Individu
slug: konseling-individu
icon: 👤
description: Pendampingan untuk membantu menghadapi masalah pribadi, stres, overthinking, kecemasan, dan pengembangan diri.
is_active: true
```

```text
name: Konseling Pasangan
slug: konseling-pasangan
icon: 💞
description: Membantu memahami komunikasi, menyelesaikan konflik hubungan, dan membangun hubungan yang lebih sehat bersama pasangan.
is_active: true
```

```text
name: Konseling Keluarga
slug: konseling-keluarga
icon: 👨‍👩‍👧
description: Membantu membangun hubungan keluarga yang lebih harmonis, sehat, dan saling memahami antaranggota keluarga.
is_active: true
```

---

## 3. Tabel counseling_sessions

Fungsi:

Menyimpan sesi konseling milik user.

Kolom:

```text
id
user_id
counseling_service_id
title
status
created_at
updated_at
```

Detail:

```text
user_id               : foreign id ke users
counseling_service_id : foreign id ke counseling_services
title                 : string, nullable
status                : string/enum, default active
```

Status:

```text
active
finished
```

Aturan:

- Satu user bisa memiliki banyak sesi konseling.
- Satu sesi hanya dimiliki oleh satu user.
- User hanya boleh membuka sesi miliknya sendiri.
- Jika status `finished`, user tidak boleh mengirim pesan baru.

---

## 4. Tabel chat_messages

Fungsi:

Menyimpan pesan chat dalam sesi konseling.

Kolom:

```text
id
counseling_session_id
user_id
sender
message
created_at
updated_at
```

Detail:

```text
counseling_session_id : foreign id ke counseling_sessions
user_id               : foreign id ke users, nullable
sender                : string/enum
message               : text
```

Sender:

```text
user
ai
```

Aturan:

- Jika pesan dikirim user, `sender = user` dan `user_id` diisi.
- Jika pesan dikirim AI dummy, `sender = ai` dan `user_id` boleh null.
- Pesan tidak boleh kosong.
- Pesan maksimal 1000 karakter untuk tahap awal.

---

## Relasi Model

### User

```text
User hasMany CounselingSession
User hasMany ChatMessage
```

### CounselingService

```text
CounselingService hasMany CounselingSession
```

### CounselingSession

```text
CounselingSession belongsTo User
CounselingSession belongsTo CounselingService
CounselingSession hasMany ChatMessage
```

### ChatMessage

```text
ChatMessage belongsTo CounselingSession
ChatMessage belongsTo User
```

---

## Seeder

Buat seeder:

```text
CounselingServiceSeeder
```

Seeder harus mengisi 3 layanan:

1. Konseling Individu
2. Konseling Pasangan
3. Konseling Keluarga

Seeder harus aman dijalankan ulang.

Gunakan `updateOrCreate` agar data tidak duplikat.

---

## Validasi Database dan Keamanan

- Jangan mengganti nama tabel sembarangan.
- Jangan mengganti nama kolom tanpa alasan kuat.
- Jangan menghapus migration lama tanpa izin.
- Jangan menjalankan `migrate:fresh` tanpa izin.
- Jika migration sudah pernah dijalankan, buat migration baru untuk perubahan.
- Pastikan foreign key menggunakan cascade atau null sesuai kebutuhan.
- Pastikan user tidak bisa mengakses data sesi milik user lain.
