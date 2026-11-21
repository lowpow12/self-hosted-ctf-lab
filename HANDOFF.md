# HANDOFF — konteks pengembangan

Ringkasan agar sesi/orang baru (atau Claude Code di mesin lain) langsung paham
keadaan projek tanpa mengulang dari nol. Dibuat 2026-11-21.

## Apa ini

CTF Lab pribadi untuk belajar: kumpulan mesin sengaja-rentan, bisa dijalankan
di **NetHunter/Termux (mode lite)** dan **Linux ber-Docker (mode docker)**.
Mulai baca: [`README.md`](README.md) → [`KURIKULUM.md`](KURIKULUM.md).

## Status (per 2026-11-21)

- **8 mesin** (01–08), semua murni PHP kecuali 08 (pwn, C). Tiap mesin punya:
  `machine.json`, aplikasi rentan, `Dockerfile`+`entrypoint.sh`, `README.md`
  (hint), `MATERI.md` (modul teori), `solution/WRITEUP.md`, `solution/exploit.sh`
  (solver otomatis mode-agnostik).
- **Tooling** `labctl`: `list`, `lite up|down|status|run`, `docker build|up|down|status`,
  `check <id> <flag>`, `selftest <lite|docker> [id|all]`, `flags`. Plus `ctf-tmux`
  (sesi tmux 2 panel). Helper `.pj.py` (baca JSON tanpa jq).
- **Pengujian:** `./labctl selftest lite all` → **8/8 PASS** di NetHunter (aarch64).
  Mode **docker BELUM diverifikasi** (mesin pengembangan tanpa Docker) — validasi
  dengan `./labctl selftest docker all`, lihat [`docs/DOCKER-TESTING.md`](docs/DOCKER-TESTING.md).

| ID | Kategori | Teknik |
|----|----------|--------|
| 01-pingboard | web→privesc | command injection, filter bypass, sudo find |
| 02-vaultdocs | web | path traversal / LFI |
| 03-snapgallery | web→RCE | upload MIME bypass, webshell, SUID bash |
| 04-cookiejar | web auth | cookie base64 tanpa tanda tangan |
| 05-renderbox | web | SSTI (eval) → RCE |
| 06-saveslot | web | PHP object injection (unserialize) |
| 07-tokenforge | crypto/web | JWT alg:none + secret HMAC lemah |
| 08-securebox | pwn | stack overflow menimpa variabel authed |

## Hal penting yang mudah terlupa (gotchas)

- **flags.env** (flag asli) di-gitignore. Salin manual saat pindah mesin, atau
  `cp flags.env.example flags.env` lalu pakai nilai contoh.
- **Mode lite** menjalankan server lewat `php -S` / fork-server python; `.run/`
  (state runtime) di-gitignore.
- **CWD di lite berbeda-beda** saat cari lokasi flag: webshell di `uploads/` →
  `../../user.txt`; `__destruct` (06) CWD = folder instance → `user.txt`.
- **Solver RCE JANGAN pakai `find /`** di NetHunter (filesystem Android raksasa →
  hang). Pakai `cat` daftar kandidat path.
- **Jangan `pkill -f "php -S …"`** — polanya ikut cocok dengan shell sesi sendiri.
  Matikan lewat PID.
- **PWN aarch64:** ret2win rumit (saved LR di bawah buffer + alignment SP → SIGBUS);
  dipakai overflow variabel-bersebelahan (deterministik, offset 32).
- Lokasi flag docker: 01 `/home/pingu`, 02 `/home/analyst`, 03 `/home/snapper`,
  04–07 `/var/www/user.txt`, 08 `/opt/securebox/flag.txt`.

## Ide/next yang belum dikerjakan

- Verifikasi penuh **mode docker** di mesin ber-Docker (`selftest docker all`).
- Mesin baru: SSRF, crypto murni offline, atau "hard" multi-tahap.
- Flag `root` (privesc) baru bermakna di docker: 01 (sudo find), 03 (SUID bash).

## Cara menambah mesin

Lihat [`docs/ADDING-MACHINES.md`](docs/ADDING-MACHINES.md). Template di
`templates/machine/`.
