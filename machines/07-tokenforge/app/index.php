<?php
// TokenForge - sesi berbasis JWT (implementasi manual).
// INTENTIONALLY VULNERABLE:
//  (1) verifikasi menerima header alg:"none" -> tanda tangan diabaikan.
//  (2) secret HMAC sangat lemah ("secret") -> bisa dipecahkan & dipakai menandatangani.

const JWT_SECRET = 'secret';   // :( sangat lemah

function b64url_encode($d) { return rtrim(strtr(base64_encode($d), '+/', '-_'), '='); }
function b64url_decode($s) {
    $s = strtr($s, '-_', '+/');
    $pad = strlen($s) % 4;
    if ($pad) $s .= str_repeat('=', 4 - $pad);
    return base64_decode($s);
}

function jwt_sign($payload, $secret) {
    $h = b64url_encode(json_encode(['alg' => 'HS256', 'typ' => 'JWT']));
    $p = b64url_encode(json_encode($payload));
    $sig = b64url_encode(hash_hmac('sha256', "$h.$p", $secret, true));
    return "$h.$p.$sig";
}

function jwt_verify($token, $secret) {
    $parts = explode('.', $token);
    if (count($parts) < 2) return null;
    [$h, $p] = $parts;
    $s = $parts[2] ?? '';
    $header  = json_decode(b64url_decode($h), true);
    $payload = json_decode(b64url_decode($p), true);
    if (!is_array($header) || !is_array($payload)) return null;

    $alg = $header['alg'] ?? '';
    if ($alg === 'none') {
        return $payload;                          // <-- VULN: tanpa verifikasi!
    }
    if ($alg === 'HS256') {
        $calc = b64url_encode(hash_hmac('sha256', "$h.$p", $secret, true));
        if (hash_equals($calc, $s)) return $payload;
    }
    return null;
}

function flag_text() {
    $f = dirname(__DIR__) . '/user.txt';
    return is_file($f) ? trim(file_get_contents($f)) : 'FLAG{tidak_ditemukan}';
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'])) {
    $u = trim($_POST['username']);
    if ($u !== '' && ($_POST['password'] ?? '') === 'letmein') {
        $tok = jwt_sign(['user' => $u, 'role' => 'user'], JWT_SECRET);
        setcookie('token', $tok, 0, '/');
        header('Location: index.php'); exit;
    }
    $error = 'Login gagal. (demo: password = letmein)';
}
if (isset($_GET['logout'])) { setcookie('token', '', time() - 3600, '/'); header('Location: index.php'); exit; }

$claims = !empty($_COOKIE['token']) ? jwt_verify($_COOKIE['token'], JWT_SECRET) : null;
?>
<!DOCTYPE html><html lang="id"><head><meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>TokenForge</title>
<style>body{font-family:system-ui,sans-serif;background:#0d1b2a;color:#e0e6ed;max-width:560px;margin:40px auto;padding:0 16px}
.card{background:#1b263b;border:1px solid #2e3e55;border-radius:10px;padding:20px}
input{width:100%;box-sizing:border-box;padding:9px;margin:6px 0;background:#0d1b2a;color:#e0e6ed;border:1px solid #2e3e55;border-radius:6px}
button{background:#4895ef;border:0;color:#fff;padding:9px 16px;border-radius:6px;font-weight:bold;cursor:pointer}
.flag{background:#10241a;border:1px solid #2f6b46;padding:10px;border-radius:6px;font-family:monospace}
code{background:#0d1b2a;padding:2px 5px;border-radius:4px;word-break:break-all}a{color:#4895ef}.err{color:#ff6b6b}</style></head><body>
<div class="card">
<h2>🔐 TokenForge</h2>
<?php if ($claims): ?>
  <p>Token terverifikasi. Halo <b><?= htmlspecialchars($claims['user'] ?? '?') ?></b>,
     role: <b><?= htmlspecialchars($claims['role'] ?? '?') ?></b>.</p>
  <?php if (($claims['role'] ?? '') === 'admin'): ?>
    <p>Area admin. Rahasia:</p>
    <p class="flag"><?= htmlspecialchars(flag_text()) ?></p>
  <?php else: ?>
    <p>Hanya <b>admin</b> yang boleh melihat rahasia. Token Anda role biasa.</p>
  <?php endif; ?>
  <p>Token Anda: <code><?= htmlspecialchars($_COOKIE['token'] ?? '') ?></code></p>
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
