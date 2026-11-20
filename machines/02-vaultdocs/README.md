# 02 · VaultDocs

| | |
|---|---|
| Kesulitan | Easy |
| Kategori | Web (Path Traversal / LFI) |
| Port | Docker: `8002` · Lite: `9002` |
| Flag | `user` |

## Skenario

VaultDocs adalah portal untuk membaca dokumen internal. Anda memilih sebuah
dokumen, lalu aplikasi menampilkannya. Dibuat buru-buru dan "belum sempat
di-review keamanan". Buktikan kenapa itu masalah.

## Tujuan

Baca file flag **user** yang berada **di luar** folder web aplikasi.

## Hint (buka kalau mentok)

<details><summary>Hint 1</summary>Perhatikan alamat URL saat Anda klik sebuah dokumen. Ada parameter <code>?doc=</code>. Apa yang terjadi kalau Anda ganti nilainya?</details>
<details><summary>Hint 2</summary>Aplikasi mengambil file dari sebuah folder. Bagaimana cara "naik" keluar dari folder itu di sistem file? (Petunjuk: <code>../</code>)</details>
<details><summary>Hint 3</summary>Coba baca file sistem yang pasti ada dulu, mis. <code>/etc/passwd</code>, untuk memastikan traversal jalan. Lalu arahkan ke lokasi flag.</details>
<details><summary>Hint 4 (lokasi flag)</summary>Mode lite: flag ada 2 folder di atas folder dokumen (<code>?doc=../../user.txt</code>). Mode docker: flag di home user <code>analyst</code>.</details>

Pembahasan lengkap (SPOILER): [`solution/WRITEUP.md`](solution/WRITEUP.md)
