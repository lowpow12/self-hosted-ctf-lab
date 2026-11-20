# 04 · CookieJar

| | |
|---|---|
| Kesulitan | Easy |
| Kategori | Web (Authentication / Authorization) |
| Port | Docker: `8004` · Lite: `9004` |
| Flag | `user` |

> 📘 **Baru di topik ini?** Baca modulnya dulu: [`MATERI.md`](MATERI.md)

## Skenario

CookieJar punya area member. Setelah login Anda jadi "member" biasa — tapi hanya
"admin" yang bisa melihat data rahasia. Masalahnya, bagaimana cara aplikasi tahu
Anda admin atau bukan?

## Tujuan

Lihat data rahasia (flag) yang seharusnya hanya untuk admin.

## Hint

<details><summary>Hint 1</summary>Login dulu (password demo untuk semua akun: <code>password123</code>). Lalu lihat cookie apa yang di-set browser Anda.</details>
<details><summary>Hint 2</summary>Cookie <code>session</code> itu base64. Decode isinya. Apa yang Anda lihat? Bisakah diubah?</details>
<details><summary>Hint 3</summary>Ubah <code>role</code> dari <code>member</code> jadi <code>admin</code>, encode lagi ke base64, pasang sebagai cookie, lalu buka halaman lagi.</details>

Pembahasan lengkap (SPOILER): [`solution/WRITEUP.md`](solution/WRITEUP.md)
