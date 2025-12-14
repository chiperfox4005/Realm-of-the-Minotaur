<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/layout.php';

start_session();
if (!empty($_SESSION['user_id'])) redirect(BASE_URL . '/player/dashboard.php');

$error = '';
$ok = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = trim($_POST['username'] ?? '');
  $password = (string)($_POST['password'] ?? '');

  if ($username === '' || $password === '') {
    $error = "Username dan password wajib diisi.";
  } else {
    try {
      db()->prepare("INSERT INTO users (username, password, role, gacha_tokens) VALUES (?, ?, 'user', 2)")
         ->execute([$username, $password]);
      $ok = "Akun berhasil dibuat. Silakan login.";
    } catch (PDOException $e) {
      $error = "Username sudah dipakai.";
    }
  }
}

render_header('Register');
?>
<div class="center-container">
  <div class="panel auth-panel">
    <div class="h1">Register</div>
    <div class="muted">Buat akun baru untuk masuk ke dunia Minotaur.</div>
    <hr class="sep">
    <?php if ($error): ?><div class="msg err"><?= esc($error) ?></div><?php endif; ?>
    <?php if ($ok): ?><div class="msg ok"><?= esc($ok) ?></div><?php endif; ?>

    <form method="post">
      <label>Username</label>
      <input class="input-theme" name="username" required placeholder="username...">
      <label>Password</label>
      <input class="input-theme" name="password" type="password" required placeholder="password...">
      <div style="margin-top:12px; display:flex; gap:10px; flex-wrap:wrap;">
        <button class="btn btn-primary" type="submit">Buat Akun</button>
        <a class="btn" href="login.php">Kembali</a>
      </div>
    </form>
  </div>
</div>
<?php render_footer(); ?>
