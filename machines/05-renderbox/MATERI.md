# Modul 5 · Server-Side Template Injection (SSTI)

> Lab: **05-renderbox** · Kesulitan: medium · Prasyarat: Modul 1 (konsep injeksi)

## Yang akan Anda pelajari

- Apa itu template engine dan di mana injeksi bisa terjadi.
- Perbedaan "menampilkan data" vs "mengevaluasi ekspresi".
- Cara mendeteksi SSTI dan menaikkannya jadi RCE.
- Bahaya `eval()` pada input pengguna.

## Teori singkat

Template engine menggabungkan **template** (tata letak) dengan **data**. Contoh
aman: `Halo {{ nama }}` di mana `{{ nama }}` diganti nilai variabel.

SSTI terjadi kalau **input pengguna ikut menjadi bagian template** dan engine
**mengevaluasi** isinya, bukan sekadar menampilkannya. Pada engine buatan sendiri
yang memakai `eval()`:

```php
preg_replace_callback('/\{\{(.+?)\}\}/', fn($m) => eval("return {$m[1]};"), $tpl);
```

`{{ 7*7 }}` menghasilkan `49` (dievaluasi!). Karena ini PHP, `{{ system("id") }}`
menjalankan perintah → **RCE**.

### Deteksi

Kirim ekspresi matematika sederhana:
- Output `49` dari `{{7*7}}` → **dievaluasi** (SSTI).
- Output harfiah `7*7` → aman (hanya ditampilkan).

Payload uji tergantung engine (Jinja2, Twig, Freemarker, dll.) tapi pola "angka
yang terhitung" adalah sinyal universal.

## Cara mengenali

- Fitur yang membiarkan pengguna menyusun teks berpola: kartu ucapan, email
  template, laporan, nama tampilan yang dirender ulang.
- Uji dengan `{{7*7}}`, `${7*7}`, `#{7*7}`, `<%= 7*7 %>` sesuai teknologi.

## Praktik (lab 05-renderbox)

```bash
./ctf-tmux 05-renderbox
curl -s $TARGET/ --data-urlencode 'template={{ 7*7 }}'              # deteksi -> 49
curl -s $TARGET/ --data-urlencode 'template={{ system("id") }}'    # RCE
```

**Target:** RCE → flag `user`. Lihat path flag di [`README.md`](README.md).
Pembahasan: [`solution/WRITEUP.md`](solution/WRITEUP.md).

## Checklist lulus

- [ ] Anda membedakan output "dievaluasi" vs "ditampilkan".
- [ ] Anda menjalankan perintah sistem lewat template.
- [ ] Anda membaca flag `user`.
- [ ] Anda paham kenapa `eval()` input = RCE.

## Dunia nyata

SSTI terkenal di aplikasi dengan Jinja2 (Flask), Twig, Freemarker, Velocity. Banyak
bounty besar berasal dari SSTI → RCE. Bedakan dari XSS: SSTI berjalan di **server**.

## Pertahanan

- **Jangan pernah `eval()` input pengguna.**
- Jangan render string template yang berasal dari pengguna; pisahkan template
  (statis) dari data (dinamis).
- Kalau butuh template dari pengguna, pakai engine ber-sandbox dan matikan fungsi
  berbahaya.

## Lanjut

➡️ **Modul 6 · [Insecure Deserialization](../06-saveslot/MATERI.md)** (lab 06-saveslot)
