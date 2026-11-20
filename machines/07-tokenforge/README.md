# 07 · TokenForge

| | |
|---|---|
| Kesulitan | Medium |
| Kategori | Web / Crypto (JWT) |
| Port | Docker: `8007` · Lite: `9007` |
| Flag | `user` |

> 📘 **Baru di topik ini?** Baca modulnya dulu: [`MATERI.md`](MATERI.md)

## Skenario

TokenForge memakai JWT (JSON Web Token) untuk sesi. Role Anda (`user`/`admin`)
disimpan di dalam token. Hanya `admin` yang bisa melihat rahasia. Bisakah Anda
"menempa" token yang mengaku admin?

## Tujuan

Dapatkan akses admin dan baca flag **user**.

## Hint

<details><summary>Hint 1</summary>Login (password demo: <code>letmein</code>). Token Anda ditampilkan — itu 3 bagian dipisah titik: <code>header.payload.signature</code>, masing-masing base64url. Decode payload: ada <code>"role":"user"</code>.</details>
<details><summary>Hint 2 (alg:none)</summary>Standar JWT punya algoritma <code>none</code> (tanpa tanda tangan). Kalau verifier menerimanya, Anda bisa membuat token sendiri: header <code>{"alg":"none"}</code>, payload <code>{"role":"admin"}</code>, signature dikosongkan (tetap sisakan titik di akhir).</details>
<details><summary>Hint 3 (jalur lain: secret lemah)</summary>Kalau <code>alg:none</code> ditutup, secret HMAC-nya mungkin bisa ditebak/di-crack (mis. dengan <code>hashcat -m 16500</code> atau john). Secret di mesin ini sangat lemah. Setelah tahu secret, tandatangani token admin Anda sendiri (HS256).</details>

Pembahasan lengkap (SPOILER): [`solution/WRITEUP.md`](solution/WRITEUP.md)
