# Menguji Mode Docker

Mode lite (NetHunter) sudah diuji menyeluruh. Mode docker **belum** bisa diuji di
lingkungan pengembangan (NetHunter/Termux tidak punya Docker). Dokumen ini cara
memvalidasinya di komputer ber-Docker (Linux/VPS/WSL2/Mac).

## Prasyarat

- Docker terpasang dan daemon jalan (`docker info` sukses).
- `git`, `curl`, dan `nc` (atau `ncat`) tersedia.
- Clone repo ini, lalu:
  ```bash
  cd ctf-lab
  cp flags.env.example flags.env     # boleh langsung pakai nilai contoh
  ```

## Uji otomatis semua mesin (disarankan)

```bash
./labctl selftest docker all
```

Untuk tiap mesin, perintah ini: **build image → jalankan container → tunggu siap →
jalankan solver otomatis (`machines/<id>/solution/exploit.sh`) → cocokkan flag →
hentikan**. Hasil akhir berupa ringkasan `PASS/FAIL`.

Uji satu mesin saja:

```bash
./labctl selftest docker 03-snapgallery
```

> Catatan: build pertama mengunduh base image (`php:8.2-apache`, `debian-slim`) —
> perlu internet dan beberapa menit. Build berikutnya memakai cache.

## Uji manual (kalau ingin mengeksplor)

```bash
./labctl docker up 01-pingboard         # http://127.0.0.1:8001
# ... serang manual, atau jalankan solvernya:
TARGET=http://127.0.0.1:8001 sh machines/01-pingboard/solution/exploit.sh
./labctl docker down 01-pingboard
```

Mesin pwn (08) dihubungi via nc:

```bash
./labctl docker up 08-securebox         # port 8008
TARGET='127.0.0.1 8008' sh machines/08-securebox/solution/exploit.sh
```

## Solver itu mode-agnostik

Skrip `solution/exploit.sh` dibuat agar jalan di **lite maupun docker** (mencoba
beberapa kandidat lokasi flag). Solver-solver ini sudah **lolos 100% di mode lite**
(`./labctl selftest lite all` → 8 PASS), jadi logika serangannya terbukti benar;
`selftest docker` memvalidasi bahwa **pengemasan Docker** (build, entrypoint, flag,
server) juga benar.

## Kalau ada yang FAIL

- Lihat log container: `docker logs ctflab-<id>`.
- Pastikan flag di dalam container: `docker exec ctflab-<id> sh -c 'cat <path-flag>'`
  (path flag docker tiap mesin ada di `entrypoint.sh`-nya).
- Port bentrok? Pastikan 8001–8008 bebas di host.
- Laporkan mesin mana yang gagal + isi `docker logs` agar bisa diperbaiki.

## Privilege escalation (root) di docker

Beberapa mesin punya flag `root` yang hanya bermakna di docker (isolasi container):
- **01-pingboard**: `sudo find ... -exec` (GTFOBins).
- **03-snapgallery**: SUID `/bin/bash` → `bash -p`.

Solver otomatis hanya mengambil flag `user`. Flag `root` dikerjakan manual sesuai
`solution/WRITEUP.md` masing-masing.
