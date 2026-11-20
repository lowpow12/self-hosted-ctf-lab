# Menambah Mesin Baru

1. **Salin template:**
   ```bash
   cp -r templates/machine machines/02-namamu
   ```

2. **Edit `machines/02-namamu/machine.json`:**
   - `id` harus sama dengan nama folder (mis. `02-namamu`).
   - `ports[0].host` = port Docker unik (pola `80NN`).
   - `lite.port` = port lite unik (pola `90NN`). Hapus seluruh blok `lite`
     jika mesin ini tidak bisa jalan tanpa Docker.
   - `flags` = slot flag yang ada, mis. `["user","root"]` atau `["user"]`.

3. **Tulis aplikasi rentan** di `app/` sesuai `techniques`.

4. **Flag via environment.** Jangan hardcode flag di image/kode.
   `labctl` menyuntikkan env `FLAG_<SLOT>` (mis. `FLAG_USER`, `FLAG_ROOT`)
   dari `flags.env`. `entrypoint.sh` menuliskannya ke file lalu meng-`unset`-nya.
   Tambahkan baris di `flags.env`:
   ```
   FLAG_02_NAMAMU_USER=FLAG{...}
   FLAG_02_NAMAMU_ROOT=FLAG{...}
   ```
   (Pola nama: `FLAG_<ID uppercase, non-alnum jadi _>_<SLOT>`.)

5. **Tulis writeup** lengkap di `solution/WRITEUP.md` dan hint bertahap di `README.md`.

6. **Uji:**
   ```bash
   ./labctl list
   ./labctl lite up 02-namamu     # atau: ./labctl docker up 02-namamu
   ```

## Konvensi mode lite

Blok `lite` di `machine.json`:

| Field | Arti |
|---|---|
| `port` | port yang didengarkan |
| `copy` | `{"<folder-sumber>": "<nama-docroot>"}` — folder disalin ke instance dir |
| `cmd` | perintah start; `{bind}` & `{port}` diganti otomatis |
| `flag_files.user` | path (relatif instance dir) tempat flag `user` ditulis |
| `note` | catatan keterbatasan, ditampilkan saat `up` |

Mesin lite jalan sebagai proses biasa (tanpa isolasi), jadi biasanya hanya
cocok untuk flag `user`/level aplikasi. Privesc ke root hanya bermakna di Docker.

## Ide kategori berikutnya

web (SQLi, LFI/upload, SSRF, deserialization, auth bypass) · pwn (buffer
overflow, format string) · crypto · privesc Linux (SUID, cron, PATH, capabilities) ·
network/pivoting · misconfig (secrets, .git exposure).
