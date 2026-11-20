<?php
// PingBoard - NetOps internal diagnostic panel
// INTENTIONALLY VULNERABLE: command injection with a weak blacklist filter.

$output = null;
$error = null;
$host = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $host = isset($_POST['host']) ? $_POST['host'] : '';

    // "Security" added by an intern after the last pentest report.
    $blacklist = [';', '&&', '`'];
    $blocked = false;
    foreach ($blacklist as $bad) {
        if (strpos($host, $bad) !== false) {
            $blocked = true;
            break;
        }
    }

    if ($host === '') {
        $error = 'Host tidak boleh kosong.';
    } elseif ($blocked) {
        $error = 'Karakter berbahaya terdeteksi! Insiden ini sudah dicatat.';
    } else {
        $output = shell_exec('ping -c 2 -W 1 ' . $host . ' 2>&1');
        if ($output === null || $output === '') {
            $output = '(tidak ada output)';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>PingBoard &middot; NetOps</title>
<style>
  body { font-family: ui-monospace, Menlo, Consolas, monospace; background:#0f1419; color:#c5d0da; margin:0; }
  header { background:#16202a; padding:14px 24px; border-bottom:1px solid #253240; }
  header b { color:#4fc3f7; }
  main { max-width:760px; margin:32px auto; padding:0 16px; }
  .card { background:#16202a; border:1px solid #253240; border-radius:6px; padding:20px; }
  input[type=text] { width:70%; padding:8px; background:#0f1419; color:#c5d0da; border:1px solid #33475b; border-radius:4px; }
  button { padding:8px 16px; background:#4fc3f7; color:#0f1419; border:0; border-radius:4px; cursor:pointer; font-weight:bold; }
  pre { background:#0b0f13; padding:12px; border-radius:4px; overflow-x:auto; white-space:pre-wrap; }
  .err { color:#ff6b6b; }
  small { color:#6b7d8f; }
</style>
</head>
<body>
<header><b>PingBoard</b> &mdash; NetOps Internal Diagnostics v1.3</header>
<main>
  <div class="card">
    <p>Cek konektivitas host di jaringan internal.</p>
    <form method="post">
      <input type="text" name="host" placeholder="contoh: 10.0.0.1" value="<?= htmlspecialchars($host) ?>">
      <button type="submit">Ping</button>
    </form>
    <?php if ($error): ?>
      <p class="err"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <?php if ($output !== null): ?>
      <h4>Hasil:</h4>
      <pre><?= htmlspecialchars($output) ?></pre>
    <?php endif; ?>
  </div>
  <p><small>Hanya untuk penggunaan internal. Hubungi pingu@netops.local jika ada masalah.</small></p>
</main>
</body>
</html>
