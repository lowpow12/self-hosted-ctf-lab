# 06 · SaveSlot

| | |
|---|---|
| Kesulitan | Medium |
| Kategori | Web (PHP Object Injection / Insecure Deserialization) |
| Port | Docker: `8006` · Lite: `9006` |
| Flag | `user` |

> 📘 **Baru di topik ini?** Baca modulnya dulu: [`MATERI.md`](MATERI.md)

## Skenario

SaveSlot menyimpan progres game Anda di sebuah cookie. Isi cookie itu adalah
object PHP yang "di-serialize". Saat halaman dibuka, server meng-`unserialize`
cookie tersebut — apa adanya.

## Tujuan

Manfaatkan proses deserialisasi untuk membaca file flag **user**.

## Hint

<details><summary>Hint 1</summary>Buka halaman, lihat cookie <code>slot</code>. Decode base64-nya — itu format <code>serialize()</code> PHP, mis. <code>O:8:"SaveSlot":...</code>.</details>
<details><summary>Hint 2</summary>Saat <code>unserialize()</code>, PHP membuat object dari class APA PUN yang disebut di data. Class dengan method ajaib (<code>__destruct</code>, <code>__wakeup</code>) akan menjalankan kodenya. Apakah ada class seperti itu di aplikasi? (Lihat sumbernya / tebak dari nama fitur.)</details>
<details><summary>Hint 3</summary>Ada class <code>Note</code> dengan properti <code>path</code>; <code>__destruct</code>-nya menampilkan isi file di <code>path</code>. Buat object <code>Note</code> ter-serialize dengan <code>path</code> menunjuk ke file flag, pasang sebagai cookie <code>slot</code>.</details>

Pembahasan lengkap (SPOILER): [`solution/WRITEUP.md`](solution/WRITEUP.md)
