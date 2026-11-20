# Modul 1 · OS Command Injection

> Lab: **01-pingboard** · Kesulitan: easy · Prasyarat: terminal dasar, `curl`

## Apa yang akan Anda pelajari

- Apa itu OS command injection dan kenapa berbahaya.
- Operator shell yang bisa menyambung perintah.
- Kenapa filter "blacklist" hampir selalu bisa ditembus.
- Dasar privilege escalation lewat `sudo` yang salah konfigurasi.

## Teori singkat

Banyak aplikasi menjalankan perintah sistem untuk tugas tertentu — misal nge-ping
sebuah host. Kodenya sering seperti ini:

```php
shell_exec("ping -c 2 " . $input);
```

Masalahnya: `$input` dari pengguna **digabung langsung** ke string perintah yang
dijalankan shell. Shell punya karakter spesial untuk menyambung/menggabung perintah:

| Operator | Arti |
|---|---|
| `;` | jalankan perintah berikutnya setelahnya |
| `\|` | pipe: kirim output ke perintah berikutnya |
| `\|\|` | jalankan berikutnya jika yang pertama gagal |
| `&&` | jalankan berikutnya jika yang pertama sukses |
| `$(...)` / `` `...` `` | command substitution |
| newline | pemisah baris = pemisah perintah |

Kalau input `127.0.0.1` berubah jadi `127.0.0.1; id`, shell menjalankan `ping ...`
**lalu** `id`. Itulah command injection: data pengguna "melarikan diri" jadi perintah.

### Kenapa blacklist gagal

Developer sering menambal dengan memblokir beberapa karakter (mis. `;` dan `&&`).
Tapi operator shell banyak. Blokir `;`? Masih ada `|`, `||`, `$()`, newline.
**Blacklist = menebak semua yang jahat** (mustahil). **Whitelist = hanya izinkan
yang benar** (jauh lebih aman).

## Cara mengenali

- Ada fitur yang "melakukan sesuatu di server" dari input Anda: ping, nslookup,
  convert gambar, kirim email, generate PDF.
- Output menyerupai hasil perintah sistem.
- Uji hati-hati: tambahkan `| id` atau `$(id)` dan lihat apakah muncul `uid=...`.

## Praktik (lab 01-pingboard)

```bash
./ctf-tmux 01-pingboard          # buka mesin + playground
# di panel playground:
curl -s $TARGET/ --data-urlencode 'host=127.0.0.1; id'   # coba yang diblokir
curl -s $TARGET/ --data-urlencode 'host=127.0.0.1 | id'  # operator yang lolos
```

**Target:** dapatkan flag `user`, lalu (mode docker) naik ke root via `sudo -l`.
Hint bertahap: [`README.md`](README.md). Pembahasan: [`solution/WRITEUP.md`](solution/WRITEUP.md).

## Checklist lulus

- [ ] Anda bisa menjelaskan kenapa `;` diblokir tapi `|` lolos.
- [ ] Anda dapat menjalankan `id` lewat form.
- [ ] Anda membaca flag `user`.
- [ ] (docker) Anda paham kenapa `sudo find` bisa jadi root (GTFOBins).

## Dunia nyata

Command injection rutin masuk OWASP Top 10 (kategori Injection). Contoh: Shellshock
(2014) memanfaatkan parsing variabel lingkungan jadi perintah. Dampaknya biasanya
RCE penuh = kendali server.

## Pertahanan

- Pakai API native, bukan shell (mis. library HTTP/DNS, bukan `system("ping")`).
- Kalau harus memanggil program, gunakan argumen terpisah (array), **jangan**
  string yang di-parse shell; dan `escapeshellarg()` + validasi whitelist.
- Validasi input: untuk IP/host pakai `filter_var(..., FILTER_VALIDATE_IP)`.
- Jalankan layanan dengan hak minimum; audit aturan `sudo`.

## Lanjut

➡️ **Modul 2 · [Path Traversal / LFI](../02-vaultdocs/MATERI.md)** (lab 02-vaultdocs)
