# Modul 8 · Stack Buffer Overflow (Binary Exploitation / pwn)

> Lab: **08-securebox** · Kesulitan: medium · Prasyarat: dasar C, paham memori/stack

## Yang akan Anda pelajari

- Bagaimana buffer disimpan di stack dan apa yang terjadi saat meluap.
- Menimpa variabel bersebelahan untuk mengubah alur program.
- Kenapa fungsi tanpa batas (`gets`, `read` berlebih) berbahaya.
- Pengantar proteksi modern (canary, ASLR, NX, PIE).

## Teori singkat

Variabel lokal fungsi disimpan di **stack**. Kalau program menyalin data lebih
banyak dari ukuran buffer, kelebihannya menimpa memori di sebelahnya:

```c
struct { char buf[32]; int authed; } s;   // authed tepat setelah buf
s.authed = 0;
read(0, s.buf, 128);                       // menulis sampai 128 byte ke buf[32]!
if (s.authed != 0) win();                  // menang kalau authed != 0
```

Dengan mengirim **32 byte** (mengisi `buf`) **+ beberapa byte lagi**, byte
tambahan menimpa `authed` menjadi bukan-nol → pengecekan lolos → `win()` jalan.

> Ini bentuk paling sederhana & **deterministik**: menimpa variabel di sebelah
> buffer. Lebih canggih (menimpa alamat kembali/ret2win, ROP) butuh pemahaman
> kalling convention & proteksi, dan bergantung arsitektur.

### Catatan arsitektur (ARM64 vs x86)

Di ARM64 (NetHunter), menimpa **alamat kembali** lebih rumit: saved LR berada di
bawah buffer dan ada syarat alignment SP (sering memicu SIGBUS). Menimpa variabel
bersebelahan seperti di lab ini jalan sama persis di ARM64 maupun x86-64 — itulah
kenapa kita memakainya untuk belajar konsep.

## Cara mengenali (saat ada source)

- Buffer berukuran tetap + fungsi copy tanpa batas (`gets`, `strcpy`, `scanf("%s")`,
  `read`/`memcpy` dengan panjang lebih besar dari buffer).
- Variabel "penjaga" (flag/auth/len) dekat buffer.

## Praktik (lab 08-securebox)

```bash
./ctf-tmux 08-securebox            # lite: server pwn + playground
# di playground, kirim 36 byte (32 padding + 4 pemicu) lewat nc:
python3 -c 'import sys; sys.stdout.buffer.write(b"A"*36)' | nc -q1 $TARGET
```

(`$TARGET` di playground sudah berisi `host port`, jadi `nc $TARGET` benar.)

**Target:** lewati cek sandi → picu `win()` → flag `user`.
Baca `app/vuln.c`. Hint: [`README.md`](README.md). Pembahasan: [`solution/WRITEUP.md`](solution/WRITEUP.md).

## Checklist lulus

- [ ] Anda bisa menjelaskan kenapa 32 byte + pemicu menimpa `authed`.
- [ ] Anda memicu `win()` dan membaca flag.
- [ ] Anda tahu kenapa membaca melebihi ukuran buffer berbahaya.
- [ ] Anda bisa menyebut 1–2 proteksi yang mencegahnya.

## Dunia nyata

Buffer overflow adalah kelas bug tertua dan masih relevan (terutama C/C++). Dulu
jadi dasar worm besar (Morris, Code Red, Slammer). Proteksi modern mempersulit,
tapi bug memori tetap jadi sumber CVE kritikal.

## Pertahanan

- Gunakan batas yang benar: `read(0, buf, sizeof(buf))`; hindari `gets`/`strcpy`.
- Aktifkan **stack canary** (`-fstack-protector-strong`), **ASLR/PIE**, **NX**.
  (Lab ini sengaja mematikannya agar deterministik untuk belajar.)
- Bahasa yang aman-memori (Rust, Go) menghilangkan seluruh kelas bug ini.

## Selesai 🎉

Anda menamatkan jalur belajar! Rekap konsep inti ada di
[`../../KURIKULUM.md`](../../KURIKULUM.md). Langkah lanjutan: ulangi mesin di **mode
docker** (untuk latihan privesc ke root), atau buat mesin Anda sendiri dengan
[`../../docs/ADDING-MACHINES.md`](../../docs/ADDING-MACHINES.md).
