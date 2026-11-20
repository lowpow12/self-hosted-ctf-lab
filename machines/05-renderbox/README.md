# 05 · RenderBox

| | |
|---|---|
| Kesulitan | Medium |
| Kategori | Web (Server-Side Template Injection → RCE) |
| Port | Docker: `8005` · Lite: `9005` |
| Flag | `user` |

## Skenario

RenderBox membuat kartu ucapan. Bagian dinamis ditulis di antara `{{ }}` dan
diproses server. Kalau server benar-benar "mengevaluasi" isinya... seberapa jauh
bisa kita dorong?

## Tujuan

Dapatkan eksekusi perintah di server dan baca flag **user**.

## Hint

<details><summary>Hint 1</summary>Coba render <code>{{ 7*7 }}</code>. Kalau hasilnya <code>49</code> (bukan teks apa adanya), berarti ekspresi dievaluasi server.</details>
<details><summary>Hint 2</summary>Ini PHP. Fungsi apa yang menjalankan perintah sistem? Coba <code>{{ system("id") }}</code>.</details>
<details><summary>Hint 3</summary>Flag user: lite ada di <code>../user.txt</code> relatif direktori kerja; docker di <code>/var/www/user.txt</code>. Kalau ragu: <code>{{ system("find / -name user.txt 2>/dev/null") }}</code>.</details>

Pembahasan lengkap (SPOILER): [`solution/WRITEUP.md`](solution/WRITEUP.md)
