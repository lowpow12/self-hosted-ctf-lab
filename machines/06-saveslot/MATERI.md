# Modul 6 · Insecure Deserialization (PHP Object Injection)

> Lab: **06-saveslot** · Kesulitan: medium · Prasyarat: Modul 4, dasar OOP/PHP

## Yang akan Anda pelajari

- Apa itu serialisasi/deserialisasi dan kenapa berbahaya pada input pengguna.
- "Magic method" PHP (`__wakeup`, `__destruct`) sebagai pemicu.
- Konsep **gadget** dan **POP chain**.
- Membuat payload object ter-serialize.

## Teori singkat

**Serialisasi** mengubah object jadi string agar bisa disimpan/dikirim;
**deserialisasi** mengembalikannya jadi object. Di PHP: `serialize()` /
`unserialize()`.

```
O:8:"SaveSlot":3:{s:6:"player";s:7:"Pemain1";s:5:"level";i:1;s:5:"score";i:0;}
```

Bahayanya: `unserialize()` pada data pengguna akan **membuat object dari class apa
pun** yang disebut dalam data. Jika sebuah class punya **magic method** yang
berjalan otomatis, penyerang bisa memicunya:

```php
class Note {
    public $path;
    public function __destruct() {           // jalan saat object dihancurkan
        if ($this->path) echo file_get_contents($this->path);  // baca file!
    }
}
```

Kirim object `Note` ter-serialize dengan `path` menunjuk ke file flag → saat script
selesai, `__destruct` membaca & menampilkan file. Ini **PHP Object Injection**.

- **Gadget**: class yang ada di aplikasi dan punya efek berguna di magic method.
- **POP chain**: rantai beberapa gadget untuk efek lebih kuat (file write / RCE).

## Cara mengenali

- Cookie/parameter yang didecode jadi format `serialize` PHP (`O:..:"..":..`) atau
  format serialisasi bahasa lain (Java `rO0...`, Python pickle, .NET).
- Aplikasi memanggil `unserialize()`/`pickle.loads()`/`readObject()` pada input.

## Praktik (lab 06-saveslot)

```bash
./ctf-tmux 06-saveslot
# buat payload gadget Note (lite: flag = "user.txt" karena CWD saat __destruct):
PAY=$(php -r 'class Note{public $title="x";public $path;}$n=new Note();$n->path="user.txt";echo base64_encode(serialize($n));')
curl -s -b "slot=$PAY" $TARGET/ | grep FLAG
```

**Target:** baca flag `user` lewat gadget. Buktikan dulu dengan `/etc/passwd`.
Hint: [`README.md`](README.md). Pembahasan: [`solution/WRITEUP.md`](solution/WRITEUP.md).

## Checklist lulus

- [ ] Anda bisa membaca & memahami string `serialize()`.
- [ ] Anda membuat object `Note` ter-serialize sendiri.
- [ ] Anda membaca file lewat gadget `__destruct`.
- [ ] Anda paham kenapa `unserialize()` input = bahaya.

## Dunia nyata

Insecure deserialization masuk OWASP Top 10. Kasus besar: gadget chain di Java
(ysoserial), PHP (Laravel/Symfony POP chains), Python pickle. Sering berujung RCE.

## Pertahanan

- **Jangan `unserialize()` data pengguna.** Pakai format data murni: JSON.
- Jika terpaksa: `unserialize($d, ['allowed_classes' => false])`.
- Tandatangani data yang dititipkan ke client (HMAC) agar tak bisa diubah.

## Lanjut

➡️ **Modul 7 · [Kelemahan JWT](../07-tokenforge/MATERI.md)** (lab 07-tokenforge)
