<?php
declare(strict_types=1);

require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/../config/app.php';

function render_header(string $title, string $bodyClass = 'bg-castle'): void {
  start_session();
  $u = current_user();
  ?>
  <!doctype html>
  <html lang="id">
  <head>  
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title><?= esc($title) ?></title>

    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600;700&family=Cinzel+Decorative:wght@700;900&family=Lora:ital,wght@0,400;0,600;1,400&display=swap" rel="stylesheet">
  </head>

  <body class="<?= esc($bodyClass) ?>">
    <nav class="nav">
      <div class="nav-container">
        <a class="nav-brand" href="<?= BASE_URL ?>/player/dashboard.php">Realm of the Minotaur</a>

        <ul class="nav-menu">
          <?php if ($u): ?>
            <li><a class="nav-link" href="<?= BASE_URL ?>/player/dashboard.php">Dashboard</a></li>
            <li><a class="nav-link" href="<?= BASE_URL ?>/player/profile.php">Profile</a></li>
            <li><a class="nav-link" href="<?= BASE_URL ?>/player/pets.php">Hewan</a></li>
            <li><a class="nav-link" href="<?= BASE_URL ?>/player/gacha.php">Gacha</a></li>
            <li><a class="nav-link" href="<?= BASE_URL ?>/player/game.php">Game</a></li>

            <?php if (($u['role'] ?? '') === 'admin'): ?>
              <li><a class="nav-link" href="<?= BASE_URL ?>/admin/pets.php">Admin Pets</a></li>
            <?php endif; ?>

            <li><a class="nav-link" href="<?= BASE_URL ?>/auth/logout.php">Logout (<?= esc($u['username']) ?>)</a></li>
          <?php else: ?>
            <li><a class="nav-link" href="<?= BASE_URL ?>/auth/login.php">Login</a></li>
            <li><a class="nav-link" href="<?= BASE_URL ?>/auth/register.php">Register</a></li>
          <?php endif; ?>
        </ul>
      </div>
    </nav>
  <?php
}

function render_footer(): void {
  echo "</body></html>";
}
