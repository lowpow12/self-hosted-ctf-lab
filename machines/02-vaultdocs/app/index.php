<?php
// VaultDocs - portal dokumen internal
// INTENTIONALLY VULNERABLE: path traversal / local file inclusion pada ?doc=.
// Input user digabung langsung ke path lalu di-include tanpa validasi.

$base = __DIR__ . '/pages/';
$doc  = isset($_GET['doc']) ? $_GET['doc'] : 'welcome.txt';

// Daftar dokumen "resmi" (cuma untuk ditampilkan di menu).
$menu = ['welcome.txt' => 'Selamat Datang', 'about.txt' => 'Tentang VaultDocs', 'changelog.txt' => 'Changelog'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>VaultDocs &middot; Portal Dokumen Internal</title>
<style>
  body { font-family: ui-sans-serif, system-ui, sans-serif; background:#f4f6f8; color:#243b53; margin:0; }
  header { background:#334e68; color:#fff; padding:16px 24px; }
  header b { letter-spacing:.5px; }
  .wrap { max-width:860px; margin:24px auto; padding:0 16px; display:flex; gap:20px; }
  nav { flex:0 0 200px; }
  nav a { display:block; padding:8px 12px; margin-bottom:6px; background:#fff; border:1px solid #d9e2ec;
          border-radius:6px; color:#334e68; text-decoration:none; }
  nav a:hover { background:#e6f0fb; }
  main { flex:1; background:#fff; border:1px solid #d9e2ec; border-radius:6px; padding:20px; }
  pre { white-space:pre-wrap; word-break:break-word; font-family:ui-monospace, monospace; margin:0; }
  small { color:#829ab1; }
</style>
</head>
<body>
<header><b>VaultDocs</b> &mdash; Portal Dokumen Internal</header>
<div class="wrap">
  <nav>
    <?php foreach ($menu as $file => $label): ?>
      <a href="?doc=<?= urlencode($file) ?>"><?= htmlspecialchars($label) ?></a>
    <?php endforeach; ?>
  </nav>
  <main>
    <pre><?php
      // VULN: tidak ada pembersihan path. '../' bisa keluar dari folder pages/.
      $path = $base . $doc;
      if (is_file($path)) {
          include $path;
      } else {
          echo "Dokumen tidak ditemukan: " . htmlspecialchars($doc);
      }
    ?></pre>
    <p><small>Menampilkan: <?= htmlspecialchars($doc) ?></small></p>
  </main>
</div>
</body>
</html>
