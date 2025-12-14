<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/layout.php';

$u = require_login();
if (($u['role'] ?? '') === 'admin') redirect(BASE_URL . '/admin/pets.php');

ensure_pet_slots((int)$u['id']);

$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $slot = (int)($_POST['slot'] ?? 0);

  if (isset($_POST['remove'])) {
    db()->prepare("UPDATE user_pet_slots SET user_pet_id=NULL WHERE user_id=? AND slot_no=?")
       ->execute([(int)$u['id'], $slot]);
    $msg = "Slot $slot dikosongkan.";
  }

  if (isset($_POST['rename'])) {
    $nickname = trim($_POST['nickname'] ?? '');
    $st = db()->prepare("SELECT user_pet_id FROM user_pet_slots WHERE user_id=? AND slot_no=?");
    $st->execute([(int)$u['id'], $slot]);
    $row = $st->fetch();
    if (!empty($row['user_pet_id'])) {
      db()->prepare("UPDATE user_pets SET nickname=? WHERE id=? AND user_id=?")
         ->execute([$nickname === '' ? null : $nickname, (int)$row['user_pet_id'], (int)$u['id']]);
      $msg = "Nama panggilan disimpan.";
    }
  }
}

$st = db()->prepare("
  SELECT s.slot_no, up.id AS user_pet_id, up.nickname, p.name, p.image_path, p.rarity
  FROM user_pet_slots s
  LEFT JOIN user_pets up ON up.id = s.user_pet_id
  LEFT JOIN pets p ON p.id = up.pet_id
  WHERE s.user_id=?
  ORDER BY s.slot_no ASC
");
$st->execute([(int)$u['id']]);
$slots = $st->fetchAll();

render_header('Hewan Peliharaan');
?>
<div class="container">
  <div class="panel">
    <div class="h1">Hewan Peliharaan (2 Slot)</div>
    <div class="muted">Konsep tetap: maksimal 2 pet aktif. Slot kosong bisa diisi dari Gacha.</div>
    <hr class="sep">
    <?php if ($msg): ?><div class="msg ok"><?= esc($msg) ?></div><?php endif; ?>
    <div class="grid cols-2">
      <?php foreach ($slots as $s): ?>
        <div class="card">
          <div style="display:flex; justify-content:space-between; align-items:center; gap:10px">
            <div class="h2">Slot <?= (int)$s['slot_no'] ?></div>
            <?php if (!empty($s['user_pet_id'])): ?>
              <span class="badge gold"><?= esc($s['rarity']) ?></span>
            <?php endif; ?>
          </div>

          <?php if (empty($s['user_pet_id'])): ?>
            <div class="muted">Kosong.</div>
            <div style="margin-top:12px">
              <a class="btn" href="gacha.php">Isi dari Gacha</a>
            </div>
          <?php else: ?>
            <img src="<?= esc(asset_url((string)$s['image_path'])) ?>" alt="pet" class="pet-img">

            <div style="margin-top:10px">
              <b><?= esc($s['nickname'] ?: $s['name']) ?></b>
              <div class="small">Nama asli: <?= esc($s['name']) ?></div>
            </div>

            <form method="post" style="margin-top:10px">
              <input type="hidden" name="slot" value="<?= (int)$s['slot_no'] ?>">
              <label>Nama panggilan</label>
              <input
                class="input-theme"
                type="text"
                name="nickname"
                value="<?= esc($s['nickname'] ?? '') ?>"
                placeholder="mis: Ember"
              />


              <div style="margin-top:10px; display:flex; gap:10px; flex-wrap:wrap;">
                <button class="btn wood" name="rename" value="1" type="submit">Simpan</button>
                <button class="btn danger" name="remove" value="1" type="submit" onclick="return confirm('Kosongkan slot?')">Kucilkan</button>
              </div>
            </form>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>
<?php render_footer(); ?>
