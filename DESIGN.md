# DESIGN.md - HEXA SPACE

## Konsep Desain

Desain Hexa Space harus terasa:

- Lembut
- Aman
- Nyaman
- Bersih
- Modern
- Ramah pengguna
- Tidak mengintimidasi
- Cocok untuk platform konseling digital

Gunakan style pastel dengan warna utama ungu, putih, dan pink.

---

## Warna Utama

Rekomendasi warna:

```text
Background utama : #F8F3FF
Background putih : #FFFFFF
Primary          : #C084FC
Primary dark     : #7E22CE
Secondary        : #FBCFE8
Lavender         : #E9D5FF
Soft pink        : #FCE7F3
Text dark        : #374151
Text soft        : #6B7280
Border soft      : #E5E7EB
```

---

## Font dan Nuansa

Gunakan font yang mudah dibaca.

Jika menggunakan Tailwind, gunakan default font bawaan atau font sans-serif.

Nuansa teks:

- Hangat
- Suportif
- Tidak terlalu formal
- Tetap sopan
- Bahasa Indonesia

---

## Layout Umum

Gunakan layout yang sederhana:

- Navbar di atas.
- Container dengan lebar maksimal.
- Section diberi padding cukup.
- Card dengan rounded besar.
- Shadow lembut.
- Spasi antar elemen jangan terlalu rapat.
- Responsive untuk mobile.

---

## Komponen UI

### Button Primary

Ciri:

- Background ungu pastel atau ungu sedang.
- Text putih.
- Rounded besar.
- Hover sedikit lebih gelap.

Contoh class Tailwind:

```text
bg-purple-400 hover:bg-purple-500 text-white px-6 py-3 rounded-full
```

### Button Secondary

Ciri:

- Background putih.
- Border ungu/pink.
- Text ungu.
- Rounded besar.

### Card

Ciri:

- Background putih.
- Rounded besar.
- Shadow lembut.
- Border pastel.
- Padding cukup.

### Badge

Gunakan badge untuk status:

- active
- finished
- user
- ai

### Alert

Gunakan alert jika:

- Sesi sudah selesai.
- Pesan gagal dikirim.
- Data kosong.
- Validasi gagal.

---

## Landing Page

Landing page harus memiliki section:

1. Header
2. Hero
3. Beranda
4. Apa Itu Konseling
5. Kenapa Harus Menggunakan Hexa Space
6. Bagaimana Hexa Space Membantu Klien
7. Jenis Layanan Konseling
8. Footer sederhana

---

## Konten yang Wajib Ada

### Header

```text
🫧 Hexa Space
Ruang Nyaman untuk Pulih dan Bertumbuh
Button: Masuk
```

### Hero

```text
Lagi capek sama hidup? Yuk, cerita dulu.
```

Deskripsi:

```text
Kadang isi kepala terasa penuh dan hidup jadi melelahkan. Tenang, kamu nggak harus menghadapi semuanya sendiri.
Hexa Space hadir sebagai ruang aman untuk bercerita bersama konselor berbasis AI yang siap menemani kamu kapan saja.
```

Highlight:

```text
Aman • Nyaman • 24 Jam • Rahasia Terjaga
```

---

## Konten yang Tidak Boleh Ditampilkan

Jangan tampilkan:

- Section “Pelajari Lebih Lanjut”
- Statistik angka seperti 5K+, 98%, atau 24/7 cards
- Desain terlalu ramai
- Warna terlalu gelap
- Tema medis yang terlalu kaku
- Klaim berlebihan seperti “pasti sembuh”

---

## Halaman Chat

Halaman chat harus sederhana.

Komponen:

- Header sesi
- Nama layanan
- Status sesi
- Area pesan
- Bubble pesan user
- Bubble pesan AI
- Input pesan
- Button kirim
- Button akhiri sesi

Aturan desain:

- Pesan user rata kanan.
- Pesan AI rata kiri.
- Bubble user menggunakan ungu pastel.
- Bubble AI menggunakan putih/lavender lembut.
- Jika sesi finished, input chat disembunyikan atau disable.

---

## Empty State

Jika data kosong, tampilkan pesan ramah.

Contoh:

```text
Belum ada sesi konseling. Yuk mulai cerita pertamamu.
```

```text
Belum ada pesan di sesi ini. Kamu bisa mulai bercerita kapan saja.
```

---

## Bahasa UI

Gunakan Bahasa Indonesia.

Gunakan kata-kata yang ramah:

- Mulai Konseling
- Pilih Layanan
- Lanjutkan Sesi
- Akhiri Sesi
- Kirim
- Riwayat Konseling
- Ceritakan perasaanmu di sini

Hindari kata-kata terlalu teknis di UI.
