# 08 · SecureBox

| | |
|---|---|
| Kesulitan | Medium |
| Kategori | Pwn / Binary Exploitation |
| Port | Docker: `8008` · Lite: `9008` |
| Flag | `user` |

> 📘 **Baru di topik ini?** Baca modulnya dulu: [`MATERI.md`](MATERI.md)

## Skenario

SecureBox adalah layanan pengecek kata sandi berupa **program biner** (bukan web).
Anda menghubunginya lewat jaringan dan mengetik kata sandi. Tentu Anda tidak tahu
kata sandinya — tapi programnya ceroboh soal memori.

Hubungi dengan:

```bash
nc 127.0.0.1 9008        # lite  (docker: port 8008)
```

> Binary dikompilasi otomatis saat pertama `up` (butuh `gcc`). Sumbernya ada di
> `app/vuln.c` — silakan dibaca untuk memahami bug-nya.

## Tujuan

Lewati pengecekan kata sandi dan picu fungsi yang menampilkan flag **user**.

## Hint

<details><summary>Hint 1</summary>Baca <code>vuln.c</code>. Ada <code>char buf[32]</code> tapi <code>read()</code> membaca sampai 128 byte. Ke mana kelebihan byte-nya pergi?</details>
<details><summary>Hint 2</summary>Tepat setelah <code>buf</code> ada variabel <code>authed</code> yang harus bernilai 0. Kalau Anda menulis melewati 32 byte, Anda menimpanya.</details>
<details><summary>Hint 3</summary>Kirim 32 byte 'isi' + beberapa byte bukan-nol. Contoh: <code>python3 -c 'import sys;sys.stdout.buffer.write(b"A"*36)' | nc 127.0.0.1 9008</code></details>

Pembahasan lengkap (SPOILER): [`solution/WRITEUP.md`](solution/WRITEUP.md)
