# Kurikulum CTF Lab

Jalur belajar bertahap. Tiap lab punya **modul materi** (`MATERI.md`) yang
menjelaskan konsepnya dulu, lalu Anda praktik di mesinnya. Setelah dapat flag,
`./labctl check <id> '<flag>'` akan mengarahkan ke lab berikutnya.

## Cara memakai tiap modul

1. Baca `machines/<id>/MATERI.md` — teori + cara mengenali kerentanan.
2. Nyalakan mesin: `./ctf-tmux <id>` (atau `./labctl lite up <id>`).
3. Coba pecahkan sendiri. Mentok? Buka hint di `machines/<id>/README.md`.
4. Benar-benar buntu? Baca `machines/<id>/solution/WRITEUP.md` (SPOILER).
5. Validasi: `./labctl check <id> 'FLAG{...}'` → lanjut ke lab berikutnya.

## Prasyarat umum

- Nyaman dengan terminal Linux dasar (`cd`, `ls`, `cat`), `curl`, dan membaca kode.
- Tidak perlu jago dulu — tiap modul menjelaskan dari nol.

## Jalur belajar

| # | Lab | Topik | Kesulitan | Modul |
|---|-----|-------|-----------|-------|
| 1 | 01-pingboard | OS Command Injection + privesc | easy | [MATERI](machines/01-pingboard/MATERI.md) |
| 2 | 02-vaultdocs | Path Traversal / LFI | easy | [MATERI](machines/02-vaultdocs/MATERI.md) |
| 3 | 03-snapgallery | Unrestricted File Upload → RCE | medium | [MATERI](machines/03-snapgallery/MATERI.md) |
| 4 | 04-cookiejar | Broken Auth (cookie tak ber-tanda-tangan) | easy | [MATERI](machines/04-cookiejar/MATERI.md) |
| 5 | 05-renderbox | Server-Side Template Injection | medium | [MATERI](machines/05-renderbox/MATERI.md) |
| 6 | 06-saveslot | Insecure Deserialization (PHP) | medium | [MATERI](machines/06-saveslot/MATERI.md) |
| 7 | 07-tokenforge | Kelemahan JWT | medium | [MATERI](machines/07-tokenforge/MATERI.md) |
| 8 | 08-securebox | Stack Buffer Overflow (pwn) | medium | [MATERI](machines/08-securebox/MATERI.md) |

> Urutan di atas disarankan, tapi bebas. Lab 1–2 dan 4 paling ramah untuk memulai.

## Kerangka berpikir tiap kerentanan

Hampir semua lab di sini berakar pada satu prinsip yang sama:

> **Jangan percaya input yang dikendalikan pengguna**, dan **jangan campur data
> dengan perintah/kode/path/struktur**.

Saat belajar, untuk tiap kerentanan tanyakan 4 hal:
1. **Data pengguna masuk ke mana?** (parameter, cookie, nama file, body)
2. **Dipakai sebagai apa?** (perintah shell, path file, template, SQL, object, memori)
3. **Apa pemisah data vs kode yang dilanggar?**
4. **Bagaimana cara benar memisahkannya?** (whitelist, parameterisasi, escaping, batas ukuran)
