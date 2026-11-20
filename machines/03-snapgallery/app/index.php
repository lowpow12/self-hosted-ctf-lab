<?php
// SnapGallery - galeri foto sederhana
$dir = __DIR__ . '/uploads/';
$imgs = [];
foreach (glob($dir . '*') as $p) {
    $b = basename($p);
    if ($b === '.gitkeep') continue;
    if (preg_match('/\.(jpe?g|png|gif)$/i', $b)) $imgs[] = $b;
}
?>
<!DOCTYPE html><html lang="id"><head><meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>SnapGallery</title>
<style>
 body{font-family:system-ui,sans-serif;background:#1b1f24;color:#e6e6e6;max-width:760px;margin:30px auto;padding:0 16px}
 h2{margin-bottom:4px}.sub{color:#8a94a0;margin-top:0}
 form{background:#2a2f36;padding:16px;border-radius:8px;margin:18px 0}
 input[type=file]{color:#e6e6e6}button{background:#7fd1ff;border:0;padding:8px 16px;border-radius:6px;font-weight:bold;cursor:pointer}
 .grid{display:flex;flex-wrap:wrap;gap:10px}.grid img{width:140px;height:110px;object-fit:cover;border-radius:6px;border:1px solid #3a4049}
 .empty{color:#8a94a0}
</style></head>
<body>
<h2>SnapGallery</h2>
<p class="sub">Bagikan foto tim. Format: JPEG, PNG, GIF.</p>

<form action="upload.php" method="post" enctype="multipart/form-data">
  <input type="file" name="photo" accept="image/*" required>
  <button type="submit">Upload</button>
</form>

<div class="grid">
<?php if (!$imgs): ?>
  <p class="empty">Belum ada foto. Jadilah yang pertama meng-upload!</p>
<?php else: foreach ($imgs as $b): ?>
  <img src="uploads/<?= rawurlencode($b) ?>" alt="<?= htmlspecialchars($b) ?>">
<?php endforeach; endif; ?>
</div>
</body></html>
