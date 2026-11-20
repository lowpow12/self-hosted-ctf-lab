# CTF Lab

Lab CTF pribadi untuk belajar — kumpulan mesin rentan (sengaja dibuat vulnerable)
dengan tingkat kesulitan dan teknik serangan berbeda. Dikembangkan dari
Kali NetHunter (Termux, aarch64, rootless).

> ⚠️ **Hanya untuk latihan di lingkungan sendiri.** Mesin-mesin di sini sengaja
> rentan. Jangan pernah expose ke internet publik. Default bind `127.0.0.1`.

## Dua mode menjalankan

| Mode | Untuk apa | Syarat |
|---|---|---|
| **docker** | Pengalaman lengkap (user + root flag, privesc nyata). Jalankan di PC/laptop/VPS. | Docker terpasang |
| **lite** | Latihan cepat langsung di NetHunter, tanpa Docker. Biasanya hanya flag `user`. | `python3` + `php` |

NetHunter di Termux tidak bisa menjalankan daemon Docker (tanpa cgroup/root),
jadi di HP pakai **lite**; untuk root/privesc pindahkan repo ke mesin ber-Docker
dan pakai **docker**. Kode mesinnya sama.

## Setup pertama kali (langkah demi langkah)

Baru pertama pakai? Ikuti urutan ini. Semua perintah dijalankan **dari dalam
folder `ctf-lab`**, jadi masuk dulu ke foldernya:

```bash
cd ~/projects/ctf-lab
```

**Langkah 1 — Siapkan file flag.** Flag adalah "harta" yang Anda cari di tiap
mesin (teks seperti `FLAG{...}`). Salin file contoh menjadi file asli:

```bash
cp flags.env.example flags.env
```

File `flags.env` ini sudah berisi flag siap pakai, jadi **boleh langsung lanjut**.
Kalau mau ganti isinya nanti, buka dengan editor apa pun (mis. `nano flags.env`).
File ini tidak ikut masuk git, jadi aman diubah.

**Langkah 2 — Lihat daftar mesin yang tersedia.**

```bash
./labctl list
```

Akan muncul tabel berisi ID mesin (mis. `01-pingboard`), tingkat kesulitan, dan
port. **ID itulah** yang dipakai di perintah berikutnya.

**Langkah 3 — Nyalakan satu mesin.**

```bash
./labctl lite up 01-pingboard
```

Kalau berhasil, muncul tulisan hijau seperti:

```
[+] jalan: http://127.0.0.1:9001  (pid 5572)
```

Itu artinya mesin sudah hidup. `http://127.0.0.1:9001` adalah **alamat target**
Anda — buka di browser HP atau serang pakai `curl`.

**Langkah 4 — Cek apakah mesin masih hidup (opsional, kapan saja).**

```bash
./labctl lite status 01-pingboard
```

- `UP` = mesin sedang jalan.
- `down` = mesin mati.

> 💡 **Lebih enak pakai tmux.** Daripada menjalankan server dan serangan di
> terminal yang sama, pakai `./ctf-tmux 01-pingboard` (lihat bagian berikutnya).
> Itu otomatis membuka dua panel: satu untuk mesin, satu untuk menyerang.

**Mau diakses dari perangkat lain** di WiFi yang sama (mis. serang dari laptop):

```bash
LAB_BIND=0.0.0.0 ./labctl lite up 01-pingboard
```

Lalu buka `http://<IP-HP-Anda>:9001` dari perangkat lain.

## Mematikan mesin (shutdown)

Kalau sudah selesai latihan, **matikan mesinnya** supaya tidak terus memakan
baterai/RAM dan port-nya bebas lagi.

**Mematikan satu mesin:**

```bash
./labctl lite down 01-pingboard
```

Muncul `[+] dihentikan: 01-pingboard` → berarti sudah mati. Cek ulang dengan
`./labctl lite status 01-pingboard` (harus menampilkan `down`).

**Kalau Anda pakai tmux (`ctf-tmux`):** cukup ketik `exit` di panel mesin, atau
tutup sesinya — mesin **otomatis dimatikan**, tidak perlu `down` manual.

**Lupa nama mesin yang sedang jalan?** Lihat statusnya satu per satu lewat
`./labctl list` (kolom port) lalu cek `status`, atau matikan yang Anda ingat.
Menutup terminal / HP juga akan menghentikan mesin lite (karena ia hanya proses
biasa), tapi lebih rapi kalau di-`down` dulu.

## Terminal terpisah dengan tmux (disarankan di Termux)

Supaya bisa bolak-balik antara **mesin** (server) dan **playground** (sisi
penyerang) dalam satu layar, pakai `ctf-tmux`:

```bash
./ctf-tmux 01-pingboard          # lite (default)
./ctf-tmux 01-pingboard docker   # mode docker
```

Membuka sesi tmux dua panel:

```
┌─ MESIN:01-pingboard ─────┬─ PLAYGROUND ───────────────┐
│ server jalan di sini,    │ shell penyerang,           │
│ log tiap request masuk   │ $TARGET sudah di-set,       │
│ tampil langsung          │ + contoh perintah           │
└──────────────────────────┴────────────────────────────┘
```

Navigasi tmux:

| Tombol | Fungsi |
|---|---|
| `Ctrl-b` lalu `←`/`→` | pindah antar panel |
| `Ctrl-b` lalu `z` | zoom/unzoom panel aktif (fokus satu panel) |
| `Ctrl-b` lalu `d` | detach (mesin tetap jalan di background) |
| ketik `exit` di panel mesin | menutup sesi → mesin otomatis di-down |

Menutup/kill sesi otomatis men-`down` mesinnya. `$TARGET` di panel playground
sudah berisi URL mesin, jadi bisa langsung `curl $TARGET/ ...`.

## Mode docker (di PC)

```bash
./labctl docker build 01-pingboard
./labctl docker up 01-pingboard     # http://127.0.0.1:8001
./labctl docker down 01-pingboard
```

## Struktur

```
ctf-lab/
├─ labctl                 # script manajemen (lite + docker)
├─ .pj.py                 # helper baca JSON (pengganti jq)
├─ flags.env.example      # contoh; salin ke flags.env (gitignored)
├─ machines/
│  └─ 01-pingboard/       # contoh mesin #1 (web → command injection → sudo privesc)
│     ├─ machine.json     # metadata: port, flag, teknik, config lite
│     ├─ Dockerfile, entrypoint.sh, app/
│     ├─ README.md        # brief + hint (tanpa spoiler)
│     └─ solution/WRITEUP.md
├─ templates/machine/     # kerangka untuk bikin mesin baru
└─ docs/ADDING-MACHINES.md
```

## Daftar mesin

| ID | Kesulitan | Kategori | Teknik |
|----|-----------|----------|--------|
| 01-pingboard | easy | web → privesc | command injection, bypass filter, sudo misconfig |

Tambah mesin baru: lihat [`docs/ADDING-MACHINES.md`](docs/ADDING-MACHINES.md).
