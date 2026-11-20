# 01 · PingBoard

| | |
|---|---|
| Kesulitan | Easy |
| Kategori | Web → Linux privesc |
| Port | Docker: `8001` · Lite: `9001` |
| Flag | `user` (`/home/pingu/user.txt`), `root` (`/root/root.txt`) |

> 📘 **Baru di topik ini?** Baca modulnya dulu: [`MATERI.md`](MATERI.md)

## Skenario

Tim NetOps punya panel diagnostik internal untuk nge-ping host. Setelah laporan
pentest terakhir, seorang anak magang menambahkan "filter keamanan". Tugasmu:
buktikan filter itu nggak cukup, lalu naik sampai root.

## Tujuan

1. Dapatkan eksekusi perintah di server → baca flag **user**.
2. Naikkan hak akses ke root → baca flag **root** (khusus mode Docker).

## Hint (buka kalau mentok)

<details><summary>Hint 1</summary>Perintah yang dijalankan kira-kira <code>ping -c 2 &lt;input&gt;</code>. Apa yang terjadi kalau input-mu berisi operator shell?</details>
<details><summary>Hint 2</summary>Filter-nya blacklist. Operator shell bukan cuma <code>;</code> dan <code>&amp;&amp;</code>.</details>
<details><summary>Hint 3</summary>Setelah dapat shell, cek apa yang boleh dijalankan <code>www-data</code> sebagai root.</details>

Pembahasan lengkap (SPOILER): [`solution/WRITEUP.md`](solution/WRITEUP.md)
