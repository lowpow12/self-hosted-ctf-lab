<?php
// SaveSlot - menyimpan progres "game" di cookie.
// INTENTIONALLY VULNERABLE: cookie berisi object PHP yang di-serialize, lalu
// di-unserialize tanpa validasi (PHP Object Injection). Ada class dengan method
// ajaib (__destruct) yang bisa disalahgunakan jadi pembaca file.

// Progres pemain (tidak berbahaya).
class SaveSlot {
    public $player = 'Pemain1';
    public $level  = 1;
    public $score  = 0;
}

// "Fitur lampiran catatan": kalau $path di-set, isinya ikut ditampilkan.
// Inilah gadget yang bisa disalahgunakan untuk membaca file apa pun.
class Note {
    public $title = 'catatan';
    public $path  = null;
    public function __destruct() {
        if ($this->path && is_file($this->path)) {
            echo "\n[lampiran: " . htmlspecialchars($this->title) . "]\n"
                 . htmlspecialchars(file_get_contents($this->path));
        }
    }
}

// Muat progres dari cookie, atau buat default.
if (!empty($_COOKIE['slot'])) {
    $raw = base64_decode($_COOKIE['slot'], true);
    $slot = $raw !== false ? unserialize($raw) : new SaveSlot();   // <-- VULN
} else {
    $slot = new SaveSlot();
    setcookie('slot', base64_encode(serialize($slot)), 0, '/');
}

$player = ($slot instanceof SaveSlot) ? $slot->player : 'Pemain1';
$level  = ($slot instanceof SaveSlot) ? $slot->level  : 1;
?>
<!DOCTYPE html><html lang="id"><head><meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>SaveSlot</title>
<style>body{font-family:system-ui,sans-serif;background:#10161c;color:#d7e2ea;max-width:600px;margin:40px auto;padding:0 16px}
.card{background:#182029;border:1px solid #2b3744;border-radius:10px;padding:20px}
code{background:#0c1116;padding:2px 5px;border-radius:4px}pre{white-space:pre-wrap;word-break:break-word}</style></head><body>
<div class="card">
<h2>🎮 SaveSlot</h2>
<p>Progres dimuat dari cookie Anda.</p>
<pre>Pemain : <?= htmlspecialchars((string)$player) ?>
Level  : <?= htmlspecialchars((string)$level) ?></pre>
<p>Progres disimpan di cookie <code>slot</code> (base64 dari data ter-serialize).</p>
</div>
</body></html>
