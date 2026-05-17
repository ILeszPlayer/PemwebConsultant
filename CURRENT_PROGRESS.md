# CURRENT_PROGRESS.md - HEXA SPACE

## Status Terakhir Developer

Developer sudah mulai membuat fondasi backend project Hexa Space.

Yang sudah dikerjakan:

1. Membuat migration database untuk 4 tabel utama.
2. Membuat model.
3. Membuat 5 controller.
4. Menambahkan beberapa route ke `routes/web.php`.

AI Agent wajib memeriksa file yang sudah ada sebelum melakukan perubahan.

---

## Area yang Harus Direview AI Agent

Sebelum coding, periksa:

```text
database/migrations
app/Models
app/Http/Controllers
routes/web.php
resources/views
```

Tujuan review:

1. Memastikan migration tidak bentrok.
2. Memastikan nama tabel sesuai `DATABASE_PLAN.md`.
3. Memastikan model sudah memiliki fillable.
4. Memastikan relasi model sudah benar.
5. Memastikan controller tidak kosong atau tidak bentrok.
6. Memastikan route tidak duplikat.
7. Memastikan auth middleware digunakan untuk route yang membutuhkan login.

---

## Checklist Fondasi Database

Pastikan tersedia:

```text
users
counseling_services
counseling_sessions
chat_messages
```

Pastikan users memiliki field:

```text
role
```

Pastikan counseling_services memiliki:

```text
name
slug
description
icon
is_active
```

Pastikan counseling_sessions memiliki:

```text
user_id
counseling_service_id
title
status
```

Pastikan chat_messages memiliki:

```text
counseling_session_id
user_id
sender
message
```

---

## Checklist Model

Pastikan model berikut tersedia:

```text
User
CounselingService
CounselingSession
ChatMessage
```

Pastikan relasi:

```text
User hasMany CounselingSession
User hasMany ChatMessage

CounselingService hasMany CounselingSession

CounselingSession belongsTo User
CounselingSession belongsTo CounselingService
CounselingSession hasMany ChatMessage

ChatMessage belongsTo CounselingSession
ChatMessage belongsTo User
```

---

## Checklist Controller

Controller yang disarankan:

```text
HomeController
DashboardController
CounselingServiceController
CounselingSessionController
ChatController
```

Jika nama controller berbeda, jangan langsung membuat ulang.
Cek isinya terlebih dahulu.

---

## Checklist Route

Route yang disarankan:

```text
GET /
GET /dashboard
GET /services
GET /sessions
POST /sessions
GET /sessions/{session}
PATCH /sessions/{session}/finish
POST /sessions/{session}/chat
```

Route berikut harus menggunakan middleware `auth`:

```text
/dashboard
/services
/sessions
/sessions/{session}
/sessions/{session}/chat
```

---

## Langkah Lanjutan yang Disarankan

Setelah review project, lanjutkan dengan urutan berikut:

1. Rapikan model dan relasi.
2. Buat atau rapikan seeder layanan konseling.
3. Pastikan route tidak duplikat.
4. Implementasikan logic controller satu per satu.
5. Buat Blade sederhana untuk testing.
6. Jalankan migration dan seeder jika diperlukan.
7. Tes alur login sampai chat dummy.

---

## Hal yang Tidak Boleh Dilakukan

Jangan:

- Menghapus migration lama tanpa izin.
- Menjalankan `migrate:fresh` tanpa izin.
- Mengubah nama tabel tanpa izin.
- Membuat ulang semua controller tanpa membaca yang lama.
- Menghapus route lama tanpa alasan.
- Membuat fitur besar di luar tahap awal.
