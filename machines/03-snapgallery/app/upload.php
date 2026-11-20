<?php
// SnapGallery - upload handler
// INTENTIONALLY VULNERABLE: hanya memeriksa Content-Type (MIME) yang dikirim
// client, yang sepenuhnya bisa dipalsukan. Nama & ekstensi file asli dipertahankan,
// jadi file .php yang di-upload akan dieksekusi server.

$dir = __DIR__ . '/uploads/';
$msg = '';
$ok  = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['photo'])) {
    $f = $_FILES['photo'];

    // "Validasi": percaya pada tipe yang dikirim browser. :(
    $allowed = ['image/jpeg', 'image/png', 'image/gif'];

    if ($f['error'] !== UPLOAD_ERR_OK) {
        $msg = 'Upload gagal (error ' . (int)$f['error'] . ').';
    } elseif (!in_array($f['type'], $allowed, true)) {
        $msg = 'Hanya gambar (JPEG/PNG/GIF) yang diperbolehkan. Tipe: ' . htmlspecialchars($f['type']);
    } else {
        // Nama file dipertahankan (hanya basename untuk "keamanan").
        $name = basename($f['name']);
        $dest = $dir . $name;
        if (move_uploaded_file($f['tmp_name'], $dest)) {
            $msg = 'Berhasil di-upload: ' . htmlspecialchars($name);
            $ok  = true;
        } else {
            $msg = 'Gagal menyimpan file.';
        }
    }
}

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html><html lang="id"><head><meta charset="utf-8">
<title>SnapGallery &middot; Upload</title>
<style>body{font-family:system-ui,sans-serif;background:#1b1f24;color:#e6e6e6;max-width:640px;margin:40px auto;padding:0 16px}
a{color:#7fd1ff}.msg{padding:10px 14px;border-radius:6px;background:#2a2f36;margin:14px 0}</style></head>
<body>
<h2>SnapGallery &mdash; Upload Foto</h2>
<?php if ($msg): ?><p class="msg"><?= $ok ? '✅ ' : '⚠️ ' ?><?= $msg ?></p><?php endif; ?>
<p><a href="index.php">&larr; kembali ke galeri</a></p>
</body></html>
