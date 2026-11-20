# 03 · SnapGallery

| | |
|---|---|
| Kesulitan | Medium |
| Kategori | Web (Unrestricted File Upload → RCE) |
| Port | Docker: `8003` · Lite: `9003` |
| Flag | `user`, `root` (root hanya di mode docker) |

## Skenario

SnapGallery membiarkan tim meng-upload foto. Katanya cuma menerima gambar.
Buktikan bahwa "cuma gambar" itu bohong, dapatkan eksekusi perintah, lalu
naik jadi root.

## Tujuan

1. Dapatkan **eksekusi perintah** di server (RCE) → baca flag **user**.
2. (Docker) Naik ke root → baca flag **root**.

## Hint (buka kalau mentok)

<details><summary>Hint 1</summary>Apa sebenarnya yang dicek saat Anda upload? Lihat pesan error ketika mengunggah file yang bukan gambar. Validasi ekstensi, isi file, atau sesuatu yang lain?</details>
<details><summary>Hint 2</summary>Server mempercayai <code>Content-Type</code> yang DIKIRIM oleh client. Itu bisa dipalsukan. Dengan curl/Burp, kirim file <code>.php</code> tapi set tipe-nya <code>image/png</code>.</details>
<details><summary>Hint 3</summary>File yang berhasil di-upload masuk ke folder <code>uploads/</code> dengan nama aslinya. Buka file .php itu lewat browser untuk menjalankannya.</details>
<details><summary>Hint 4 (privesc, docker)</summary>Setelah dapat shell sebagai www-data, cari file ber-SUID: <code>find / -perm -4000 2>/dev/null</code>. Ada yang tidak seharusnya.</details>

Pembahasan lengkap (SPOILER): [`solution/WRITEUP.md`](solution/WRITEUP.md)
