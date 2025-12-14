<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/db.php';

function esc(?string $s): string {
  return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8');
}

function redirect(string $path): void {
  header('Location: ' . $path);
  exit;
}

function start_session(): void {
  if (session_status() !== PHP_SESSION_ACTIVE) session_start();
}

function current_user(): ?array {
  start_session();
  if (empty($_SESSION['user_id'])) return null;

  $st = db()->prepare("SELECT id, username, role, gacha_tokens FROM users WHERE id=?");
  $st->execute([$_SESSION['user_id']]);
  $u = $st->fetch();
  return $u ?: null;
}

function require_login(): array {
  $u = current_user();
  if (!$u) redirect(BASE_URL . '/auth/login.php');
  return $u;
}

function require_admin(): array {
  $u = require_login();
  if (($u['role'] ?? '') !== 'admin') redirect(BASE_URL . '/player/dashboard.php');
  return $u;
}

function ensure_pet_slots(int $userId): void {
  $pdo = db();
  for ($i = 1; $i <= 2; $i++) {
    $pdo->prepare("INSERT IGNORE INTO user_pet_slots (user_id, slot_no, user_pet_id) VALUES (?, ?, NULL)")
        ->execute([$userId, $i]);
  }
}

function get_character(int $userId): ?array {
  $st = db()->prepare("SELECT * FROM characters WHERE user_id=?");
  $st->execute([$userId]);
  $c = $st->fetch();
  return $c ?: null;
}

/** Weighted random pet pick from pets.weight */
function roll_pet(): array {
  $pets = db()->query("SELECT id, name, image_path, rarity, weight FROM pets WHERE weight > 0")->fetchAll();
  if (!$pets) throw new RuntimeException("Tabel pets kosong.");

  $total = 0;
  foreach ($pets as $p) $total += (int)$p['weight'];

  $r = random_int(1, max(1, $total));
  foreach ($pets as $p) {
    $r -= (int)$p['weight'];
    if ($r <= 0) return $p;
  }
  return $pets[0];
}
function asset_url(string $path): string {
  $path = ltrim($path, '/');

  // encode tiap bagian path supaya spasi aman tapi "/" tetap normal
  $parts = array_map('rawurlencode', explode('/', $path));

  return BASE_URL . '/' . implode('/', $parts);
}
