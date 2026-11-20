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

## Mulai cepat (lite, di NetHunter)

```bash
cp flags.env.example flags.env      # ganti nilai flag di dalamnya
./labctl list                       # lihat daftar mesin
./labctl lite up 01-pingboard       # jalankan → http://127.0.0.1:9001
./labctl lite status 01-pingboard
./labctl lite down 01-pingboard
```

Mau diakses dari perangkat lain di WiFi yang sama:
```bash
LAB_BIND=0.0.0.0 ./labctl lite up 01-pingboard
```

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
