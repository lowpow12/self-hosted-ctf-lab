# Modul 4 · Broken Authentication & Insecure Cookies

> Lab: **04-cookiejar** · Kesulitan: easy · Prasyarat: dasar HTTP & cookie

## Yang akan Anda pelajari

- Apa itu cookie dan kenapa tidak boleh "dipercaya" apa adanya.
- Beda **autentikasi** (siapa Anda) vs **otorisasi** (boleh apa).
- Kenapa menyimpan `role` di client tanpa tanda tangan itu berbahaya.
- Konsep integritas data (tanda tangan/HMAC).

## Teori singkat

Setelah login, server perlu "mengingat" Anda. Caranya sering lewat **cookie**.
Masalah muncul kalau server menyimpan **keputusan otorisasi** di cookie yang bisa
diubah pengguna:

```
Set-Cookie: session = base64({"user":"guest","role":"member"})
```

`base64` **bukan** enkripsi dan **bukan** tanda tangan — hanya pengkodean. Siapa
pun bisa decode, ubah `role` jadi `admin`, encode lagi, dan mengirim balik. Server
yang percaya begitu saja akan memberi akses admin.

> Kunci: **data di sisi client bisa diubah client.** Kalau keputusan keamanan
> bergantung padanya tanpa integritas, itu bisa dipalsukan.

## Cara mengenali

- Buka DevTools / `curl -v`, lihat cookie setelah login.
- Isinya base64/JSON/hex yang bisa didecode dan mengandung `role`, `admin`,
  `is_admin`, `user_id`, dll.
- Tidak ada komponen tanda tangan (mis. tidak ada bagian `.<signature>`).

## Praktik (lab 04-cookiejar)

```bash
./ctf-tmux 04-cookiejar
# login (password demo: password123), lalu:
echo -n '{"user":"x","role":"admin"}' | base64          # buat cookie admin
curl -s -b 'session=<hasil-base64-di-atas>' $TARGET/ | grep FLAG
```

**Target:** lihat rahasia yang seharusnya hanya untuk admin (flag `user`).
Hint: [`README.md`](README.md). Pembahasan: [`solution/WRITEUP.md`](solution/WRITEUP.md).

## Checklist lulus

- [ ] Anda bisa decode cookie dan menjelaskan isinya.
- [ ] Anda mengubah `role` jadi `admin` dan mendapat flag.
- [ ] Anda paham beda base64 (encoding) vs tanda tangan (integritas).

## Dunia nyata

Broken Access Control adalah **#1 di OWASP Top 10 2021**. Banyak bug nyata:
mengubah `user_id` di cookie/URL (IDOR), menebak cookie admin, atau mengandalkan
nilai client untuk hak akses.

## Pertahanan

- Simpan sesi di **server** (cookie hanya berisi session id acak panjang).
- Kalau menaruh data di client, **tandatangani** (HMAC) atau pakai JWT yang
  diverifikasi dengan benar (lihat Modul 7).
- Cek otorisasi di server untuk **setiap** aksi, berbasis identitas tepercaya.

## Lanjut

➡️ **Modul 5 · [Server-Side Template Injection](../05-renderbox/MATERI.md)** (lab 05-renderbox)
