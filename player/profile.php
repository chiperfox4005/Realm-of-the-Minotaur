<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/layout.php';

$u = require_login();
if (($u['role'] ?? '') === 'admin') redirect(BASE_URL . '/admin/pets.php');

$char = get_character((int)$u['id']);
if (!$char) redirect(BASE_URL . '/player/character_create.php');

$heroImg = ($char['gender'] === 'Cewek') ? 'image/cewek.jpg' : 'image/cowok.jpg';

$st = db()->prepare("
  SELECT s.slot_no, up.nickname, p.name, p.image_path, p.rarity
  FROM user_pet_slots s
  LEFT JOIN user_pets up ON up.id = s.user_pet_id
  LEFT JOIN pets p ON p.id = up.pet_id
  WHERE s.user_id=?
  ORDER BY s.slot_no ASC
");
$st->execute([(int)$u['id']]);
$pets = $st->fetchAll();


render_header('Profile');
?>
<div class="container">
  <div class="panel">
    <div class="h1">Profile <?= esc($char['name']) ?></div>
    <div class="grid cols-2">
      <div class="card">
        <div class="h2">Kesatria</div>
        <img class="profile-img" src="<?= esc(BASE_URL . '/' . ltrim($heroImg,'/')) ?>" alt="hero">
        <div style="margin-top:10px" class="muted">
          Gender: <b><?= esc($char['gender']) ?></b><br>
          Ability: <b><?= esc($char['ability']) ?></b>
        </div>
      </div>
      <div class="card">
        <div class="h2">Pendamping (Aktif)</div>
        <?php foreach ($pets as $row): ?>
          <div style="margin-top:12px; border-top:1px solid rgba(212,175,55,.2); padding-top:12px;">
            <div class="small">Slot <?= (int)$row['slot_no'] ?></div>

            <?php if (empty($row['name'])): ?>
              <div class="muted">Kosong</div>
            <?php else: ?>
              <img
                class="profile-img"
                src="<?= esc(BASE_URL . '/' . ltrim($row['image_path'], '/')) ?>"
                alt="pet"
              >
              <div style="margin-top:10px">
                <b><?= esc($row['nickname'] ?: $row['name']) ?></b>
                <div class="small">Nama asli: <?= esc($row['name']) ?></div>
              </div>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>

        <div style="margin-top:12px">
          <a class="btn" href="pets.php">Kelola Slot</a>
        </div>
      </div>
    </div>
  </div>
</div>
<?php render_footer(); ?>
