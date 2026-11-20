# Modul 2 · Path Traversal / Local File Inclusion (LFI)

> Lab: **02-vaultdocs** · Kesulitan: easy · Prasyarat: Modul 1, paham path Linux

## Yang akan Anda pelajari

- Bagaimana aplikasi "tersesat" keluar dari folder yang seharusnya.
- Arti `../` dan cara memakainya untuk membaca file sembarang.
- Beda Path Traversal (baca file) vs LFI (meng-include file → bisa eksekusi).
- Konsep `php://filter` untuk membocorkan source code.

## Teori singkat

Aplikasi sering membuka file berdasarkan nama dari pengguna:

```php
include "pages/" . $_GET['doc'];   // ?doc=welcome.txt
```

Di Linux, `..` berarti "folder di atas". Jadi input `../../../../etc/passwd`
membuat path `pages/../../../../etc/passwd` yang **keluar** dari folder `pages/`
dan menunjuk ke `/etc/passwd`. Server dengan patuh membacanya.

- **Path Traversal**: membaca file di luar folder yang dimaksud.
- **Local File Inclusion (LFI)**: file yang dibaca itu di-*include* (dieksekusi
  sebagai kode bila berisi PHP) — lebih berbahaya, bisa berujung RCE.

### Berapa banyak `../`?

`../` yang berlebih di root (`/`) diabaikan sistem — jadi lebih aman pakai banyak:
`../../../../../../etc/passwd`. Yang penting cukup untuk mencapai `/`.

### php://filter (bonus)

Kalau Anda `include` file PHP, isinya **dieksekusi**, bukan ditampilkan. Untuk
membaca source-nya, bungkus dengan filter base64:

```
php://filter/convert.base64-encode/resource=index.php
```

Hasilnya base64 dari source (tinggal decode). Trik ini jalan hanya jika input
tidak diawali prefiks folder yang merusak skema `php://`.

## Cara mengenali

- Parameter yang berisi nama file/halaman: `?doc=`, `?page=`, `?file=`, `?lang=`.
- Uji: ganti nilainya jadi `../../../../etc/passwd` (atau `/etc/hostname`).

## Praktik (lab 02-vaultdocs)

```bash
./ctf-tmux 02-vaultdocs
curl -s "$TARGET/?doc=../../../../../../etc/passwd"   # bukti traversal
```

**Target:** baca flag `user` yang ada **di luar** web root. Lokasi tergantung
mode — lihat Hint 4 di [`README.md`](README.md). Pembahasan: [`solution/WRITEUP.md`](solution/WRITEUP.md).

## Checklist lulus

- [ ] Anda bisa membaca `/etc/passwd` lewat parameter.
- [ ] Anda paham kenapa `../` memindahkan posisi ke folder atas.
- [ ] Anda membaca flag `user`.
- [ ] Anda tahu beda "membaca file" vs "meng-include/eksekusi".

## Dunia nyata

Path traversal klasik dan masih sering muncul (mis. di fitur download/preview,
template engine, plugin). Bocornya `/etc/passwd`, konfigurasi, kunci privat, atau
source code sering jadi pijakan serangan lanjutan.

## Pertahanan

- **Whitelist**: petakan id → file yang diizinkan, jangan terima path mentah.
- Ambil `basename()` dan pastikan `realpath()` tetap di dalam folder yang boleh.
- Simpan file sensitif di luar jangkauan aplikasi; batasi hak baca proses web.

## Lanjut

➡️ **Modul 3 · [File Upload → RCE](../03-snapgallery/MATERI.md)** (lab 03-snapgallery)
