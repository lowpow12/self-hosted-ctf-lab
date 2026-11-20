<?php
// RenderBox - pembuat kartu ucapan dengan "template engine" buatan sendiri.
// INTENTIONALLY VULNERABLE: ekspresi di dalam {{ ... }} dievaluasi dengan eval().
// Ini Server-Side Template Injection (SSTI) klasik yang berujung RCE.

function render($tpl) {
    // Ganti setiap {{ ekspresi }} dengan hasil evaluasinya.
    return preg_replace_callback('/\{\{(.+?)\}\}/s', function ($m) {
        try {
            return (string) eval('return ' . $m[1] . ';');
        } catch (\Throwable $e) {
            return '[error]';
        }
    }, $tpl);
}

$tpl = $_POST['template'] ?? 'Halo {{ strtoupper("dunia") }}! 2+2 = {{ 2+2 }}';
$out = render($tpl);
?>
<!DOCTYPE html><html lang="id"><head><meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>RenderBox</title>
<style>body{font-family:system-ui,sans-serif;background:#0f1020;color:#e8e8f0;max-width:640px;margin:36px auto;padding:0 16px}
textarea{width:100%;box-sizing:border-box;height:90px;background:#1b1c33;color:#e8e8f0;border:1px solid #33355c;border-radius:8px;padding:10px;font-family:monospace}
button{background:#8a7bff;border:0;color:#fff;padding:9px 18px;border-radius:8px;font-weight:bold;cursor:pointer;margin-top:8px}
.out{margin-top:18px;background:#1b1c33;border:1px solid #33355c;border-radius:8px;padding:14px;white-space:pre-wrap}
small{color:#9a9ac0}</style></head><body>
<h2>✨ RenderBox</h2>
<p>Buat kartu ucapan. Pakai <code>{{ ekspresi }}</code> untuk bagian dinamis.</p>
<form method="post">
  <textarea name="template"><?= htmlspecialchars($tpl) ?></textarea>
  <button type="submit">Render</button>
</form>
<div class="out"><?= htmlspecialchars($out) ?></div>
<p><small>Contoh: <code>Selamat {{ date("Y") }}</code></small></p>
</body></html>
