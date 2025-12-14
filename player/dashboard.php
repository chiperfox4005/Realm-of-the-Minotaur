<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/layout.php';

$u = require_login();
if (($u['role'] ?? '') === 'admin') redirect(BASE_URL . '/admin/pets.php');

$char = get_character((int)$u['id']);
if (!$char) redirect(BASE_URL . '/player/character_create.php');

ensure_pet_slots((int)$u['id']); // pastikan slot 1-2 ada

// ambil token gacha dari tabel users
$user = current_user();
$token = (int)($user['gacha_tokens'] ?? 0);

// hitung slot aktif (berapa slot terisi)
$st = db()->prepare("SELECT COUNT(*) c FROM user_pet_slots WHERE user_id=? AND user_pet_id IS NOT NULL");
$st->execute([(int)$u['id']]);
$activeCount = (int)($st->fetch()['c'] ?? 0);

// ambil 2 slot pet aktif (slot 1 dan 2) + data petnya
$st = db()->prepare("
  SELECT s.slot_no, up.nickname, p.name, p.image_path, p.rarity
  FROM user_pet_slots s
  LEFT JOIN user_pets up ON up.id = s.user_pet_id
  LEFT JOIN pets p ON p.id = up.pet_id
  WHERE s.user_id=?
  ORDER BY s.slot_no ASC
");
$st->execute([(int)$u['id']]);
$activeSlots = $st->fetchAll();

// ambil semua pets untuk list (atau bisa filter rare/legend)
$st = db()->query("SELECT id, name, lore, rarity, image_path FROM pets ORDER BY id ASC");
$allPets = $st->fetchAll();

// helper build url image (biar aman kalau path relatif)
function img_url(?string $path): string {
  $path = trim((string)$path);
  if ($path === '') return '';
  return BASE_URL . '/' . ltrim($path, '/');
}

render_header('Dashboard');
?>

<div class="container">
  <h1 class="welcome">Selamat datang, <?= esc($char['name']) ?></h1>

  <!-- STATS TOP -->
  <div class="grid grid-3">
    <div class="stat-card">
      <div class="stat-label">Gender</div>
      <div class="stat-value"><?= esc($char['gender']) ?></div>
    </div>

    <div class="stat-card">
      <div class="stat-label">Ability</div>
      <div class="stat-value"><?= esc($char['ability']) ?></div>
    </div>

    <div class="stat-card">
      <div class="stat-label">Token Gacha</div>
      <div class="stat-value"><?= $token ?></div>
      <div class="stat-description">Dapatkan hewan pendamping.</div>
    </div>
  </div>

  <!-- ACTION CARDS -->
  <div class="grid grid-3">
    <div class="stat-card">
      <div class="stat-label">Hewan Peliharaan</div>
      <div class="stat-value"><?= $activeCount ?> / 2 Slot</div>
      <div class="stat-description">Kelola 2 slot pet aktif.</div>
      <a class="btn" style="margin-top: 1rem; width: 100%;" href="pets.php">Manage</a>
    </div>

    <div class="stat-card">
      <div class="stat-label">Game</div>
      <div class="stat-value">Battle</div>
      <div class="stat-description">Lanjutkan pertarungan melawan boss.</div>
      <a class="btn" style="margin-top: 1rem; width: 100%;" href="game.php">Battle</a>
    </div>

    <div class="stat-card">
      <div class="stat-label">Gacha</div>
      <div class="stat-value">Chance</div>
      <div class="stat-description">Dapatkan hewan pendamping.</div>
      <a class="btn" style="margin-top: 1rem; width: 100%;" href="gacha.php">Chance</a>
    </div>
  </div>

  <!-- PET AKTIF PREVIEW (gambar, bukan icon) -->
<div class="panel" style="margin-top:18px;">
  <h2 class="section-title">🐾 Pet Aktif</h2>
  <div class="grid grid-4">
    <?php foreach ($activeSlots as $slot): ?>
      <div class="hero-card">
        <div class="hero-image" style="padding:12px;">
          <?php if (!empty($slot['image_path'])): ?>
            <img
              src="<?= esc(img_url($slot['image_path'])) ?>"
              alt="pet"
              style="width:100%; height:250px; object-fit:cover; border-radius:8px;"
            >
          <?php else: ?>
            <div style="opacity:.75; font-family:'Lora',serif;">Slot kosong</div>
          <?php endif; ?>

          <?php if (!empty($slot['rarity'])): ?>
            <div class="hero-rank <?= strtolower($slot['rarity']) === 's' ? 'rank-s' : (strtolower($slot['rarity']) === 'a' ? 'rank-a' : '') ?>">
              <?= esc($slot['rarity']) ?>
            </div>
          <?php else: ?>
            <div class="hero-rank"><?= (int)$slot['slot_no'] ?></div>
          <?php endif; ?>
        </div>

        <div class="hero-content">
          <h3 class="hero-name">
            Slot <?= (int)$slot['slot_no'] ?>:
            <?= esc(($slot['nickname'] ?? '') !== '' ? $slot['nickname'] : ($slot['name'] ?? 'Kosong')) ?>
          </h3>
          <?php if (!empty($slot['name'])): ?>
            <p class="hero-description">Nama asli: <?= esc($slot['name']) ?></p>
          <?php else: ?>
            <p class="hero-description">Ambil pet dari Gacha dulu.</p>
          <?php endif; ?>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <!-- LIST SEMUA PET (gambar dari DB) -->
  <h2 class="section-title">⚔ Semua Hewan</h2>

  <div class="grid grid-4">
    <?php foreach ($allPets as $p): ?>
      <div class="hero-card">
        <div class="hero-image" style="padding:12px;">
          <?php if (!empty($p['image_path'])): ?>
            <img
              src="<?= esc(img_url($p['image_path'])) ?>"
              alt="<?= esc($p['name']) ?>"
              style="width:100%; height:250px; object-fit:cover; border-radius:8px;"
            >
          <?php endif; ?>
          <div class="hero-rank <?= strtolower($p['rarity']) === 's' ? 'rank-s' : (strtolower($p['rarity']) === 'a' ? 'rank-a' : '') ?>">
            <?= esc($p['rarity']) ?>
          </div>
        </div>

        <div class="hero-content">
          <h3 class="hero-name"><?= esc($p['name']) ?></h3>
          <p class="hero-description"><?= esc($p['lore'] ?? '') ?></p>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

</div>

<?php render_footer(); ?>
