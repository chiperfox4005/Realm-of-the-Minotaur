<?php
declare(strict_types=1);
session_start();

require_once __DIR__ . '/../config/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = trim($_POST['username'] ?? '');
  $password = (string)($_POST['password'] ?? '');

  $st = db()->prepare("SELECT id, username, password, role FROM users WHERE username = ? LIMIT 1");
  $st->execute([$username]);
  $user = $st->fetch();

  if ($user && hash_equals((string)$user['password'], $password)) {
    $_SESSION['user_id'] = (int)$user['id'];
    $_SESSION['role'] = (string)$user['role'];

    if ($user['role'] === 'admin') header("Location: ../admin/pets.php");
    else header("Location: ../player/dashboard.php");
    exit;
  } else {
    $error = "Username atau password salah.";
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - Realm of the Minotaur</title>

  <!-- Pakai path yang benar dari folder auth -->
  <link rel="stylesheet" href="../assets/css/styles.css">
  <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600;700&family=Cinzel+Decorative:wght@700;900&family=Lora:ital,wght@0,400;0,600;1,400&display=swap" rel="stylesheet">
</head>

<body class="bg-castle">
  <div class="center-container">
    <div class="panel" style="max-width: 500px; width: 100%;">
      <div class="panel-header">
        <h1 class="panel-title">Realm of the Minotaur</h1>
        <p style="color: #bdbdbd; font-size: 0.95rem; margin-top: 0.5rem;">Enter the Labyrinth</p>
      </div>

      <?php if ($error): ?>
        <div style="margin: 12px 0; padding: 10px; border: 1px solid rgba(255,85,85,.6); background: rgba(255,85,85,.12); border-radius: 10px;">
          <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
        </div>
      <?php endif; ?>

      <form method="POST" action="">
        <div class="form-group">
          <label for="username">Username</label>
          <input type="text" id="username" name="username" required>
        </div>

        <div class="form-group">
          <label for="password">Password</label>
          <input type="password" id="password" name="password" required>
        </div>

        <button type="submit" class="btn btn-primary btn-block mb-2">
          Enter the Realm
        </button>

        <div class="text-center mt-3">
          <p style="font-size: 0.9rem;">
            New adventurer?
            <a href="register.php" style="color: var(--color-gold-light); text-decoration: none; font-weight: 600;">
              Create Account
            </a>
          </p>
        </div>
      </form>

    </div>
  </div>
</body>
</html>
