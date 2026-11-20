<?php
// CookieJar - member area
// INTENTIONALLY VULNERABLE: identitas & role disimpan di cookie yang hanya
// di-base64 (tanpa tanda tangan). Siapa pun bisa mengubah role jadi "admin".

function read_session() {
    if (empty($_COOKIE['session'])) return null;
    $json = base64_decode($_COOKIE['session'], true);
    if ($json === false) return null;
    $d = json_decode($json, true);
    return is_array($d) ? $d : null;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'])) {
    $u = trim($_POST['username']);
    $p = $_POST['password'] ?? '';
    // "Autentikasi" demo: user apa pun dengan password 'password123' jadi member.
    if ($u !== '' && $p === 'password123') {
        $session = ['user' => $u, 'role' => 'member'];      // selalu 'member'
        setcookie('session', base64_encode(json_encode($session)), 0, '/');
        header('Location: index.php');
        exit;
    }
    $error = 'Login gagal. (Hint demo: password untuk semua akun = password123)';
}

if (isset($_GET['logout'])) {
    setcookie('session', '', time() - 3600, '/');
    header('Location: index.php');
    exit;
}

$sess = read_session();

// Flag hanya ditampilkan untuk admin.
function flag_text() {
    $f = dirname(__DIR__) . '/user.txt';   // lite: instance/; docker: /var/www/
    return is_file($f) ? trim(file_get_contents($f)) : 'FLAG{tidak_ditemukan}';
}
?>
<!DOCTYPE html><html lang="id"><head><meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>CookieJar</title>
<style>body{font-family:system-ui,sans-serif;background:#fffaf0;color:#3a2f1b;max-width:560px;margin:40px auto;padding:0 16px}
.card{background:#fff;border:1px solid #e9ddc5;border-radius:10px;padding:20px;box-shadow:0 1px 3px #0001}
input{width:100%;padding:9px;margin:6px 0;border:1px solid #d9c9a8;border-radius:6px;box-sizing:border-box}
button{background:#c98a3a;color:#fff;border:0;padding:9px 16px;border-radius:6px;font-weight:bold;cursor:pointer}
.err{color:#b23b3b}.flag{background:#f3ffe9;border:1px solid #bfe3a0;padding:10px;border-radius:6px;font-family:monospace}
a{color:#c98a3a}</style></head><body>
<div class="card">
<h2>🍪 CookieJar</h2>
<?php if ($sess): ?>
  <p>Halo, <b><?= htmlspecialchars($sess['user'] ?? '?') ?></b>! Role Anda:
     <b><?= htmlspecialchars($sess['role'] ?? '?') ?></b>.</p>
  <?php if (($sess['role'] ?? '') === 'admin'): ?>
     <p>Selamat datang, admin. Ini area rahasia:</p>
     <p class="flag"><?= htmlspecialchars(flag_text()) ?></p>
  <?php else: ?>
     <p>Area member biasa. Hanya <b>admin</b> yang bisa melihat data rahasia.</p>
  <?php endif; ?>
  <p><a href="?logout=1">Logout</a></p>
<?php else: ?>
  <?php if ($error): ?><p class="err"><?= htmlspecialchars($error) ?></p><?php endif; ?>
  <form method="post">
    <input name="username" placeholder="username" required>
    <input name="password" type="password" placeholder="password" required>
    <button type="submit">Masuk</button>
  </form>
<?php endif; ?>
</div></body></html>
