<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/layout.php';

$u = require_login();
if (($u['role'] ?? '') === 'admin') redirect(BASE_URL . '/admin/pets.php');

ensure_pet_slots((int)$u['id']);

start_session();
$msg = '';
$rolled = null;

function set_tokens(int $userId, int $tokens): void {
  db()->prepare("UPDATE users SET gacha_tokens=? WHERE id=?")->execute([$tokens, $userId]);
}

if (isset($_POST['reset'])) {
  set_tokens((int)$u['id'], 2);
  $msg = "Token di-reset jadi 2.";
  $u = current_user();
}

if (isset($_POST['roll'])) {
  $u = current_user();
  if ((int)$u['gacha_tokens'] <= 0) {
    $msg = "Token gacha habis.";
  } else {
    set_tokens((int)$u['id'], (int)$u['gacha_tokens'] - 1);

    $p = roll_pet();
    $rolled = $p;
    $_SESSION['last_pet_id'] = (int)$p['id'];

    db()->prepare("INSERT INTO gacha_history (user_id, pet_id) VALUES (?, ?)")
       ->execute([(int)$u['id'], (int)$p['id']]);

    $u = current_user();
  }
}

if (isset($_POST['use'])) {
  $petId = (int)($_SESSION['last_pet_id'] ?? 0);
  if ($petId <= 0) {
    $msg = "Belum ada hasil gacha.";
  } else {
    db()->prepare("INSERT INTO user_pets (user_id, pet_id, source) VALUES (?, ?, 'gacha')")
       ->execute([(int)$u['id'], $petId]);
    $userPetId = (int)db()->lastInsertId();

    $st = db()->prepare("SELECT slot_no FROM user_pet_slots WHERE user_id=? AND user_pet_id IS NULL ORDER BY slot_no ASC LIMIT 1");
    $st->execute([(int)$u['id']]);
    $slot = $st->fetch();

    if ($slot) {
      db()->prepare("UPDATE user_pet_slots SET user_pet_id=? WHERE user_id=? AND slot_no=?")
         ->execute([$userPetId, (int)$u['id'], (int)$slot['slot_no']]);
      unset($_SESSION['last_pet_id']);
      redirect(BASE_URL . '/player/pets.php');
    } else {
      $msg = "Slot penuh. Pet tetap masuk koleksi (belum dipasang).";
      unset($_SESSION['last_pet_id']);
    }
  }
}

render_header('Gacha');
?>
<div class="container">
  <div class="panel gacha-panel">
    <div class="h1">Gacha Hewan Peliharaan</div>
    <div class="muted">Token tersisa: <b><?= (int)current_user()['gacha_tokens'] ?></b></div>
    <hr class="sep">

    <?php if ($msg): ?><div class="msg err"><?= esc($msg) ?></div><?php endif; ?>

    <div class="gacha-wrap">

      <!-- pointer -->
      <div class="gacha-pointer"></div>

      <!-- wheel -->
      <div class="gacha-wheel-area">
        <div class="gacha-wheel" id="wheel">
          <!-- label (8 segmen contoh) -->
          <?php
            $labels = ["COMMON","UNCOMMON","RARE","EPIC","LEGEND","MYTHIC","BONUS","JACKPOT"];
            $step = 360 / count($labels);
            foreach ($labels as $i => $t):
              $deg = ($i * $step) + ($step/2);
          ?>
            <div class="gacha-wheel-label"
                 style="transform: rotate(<?= $deg ?>deg) translate(0,-140px) rotate(<?= -$deg ?>deg);">
              <?= esc($t) ?>
            </div>
          <?php endforeach; ?>

          <div class="gacha-wheel-center">SPIN</div>
        </div>
      </div>

      <!-- tombol di bawah wheel -->
      <form method="post" class="gacha-actions" id="gachaForm">
      <!-- ini PENTING -->
      <input type="hidden" name="roll" value="1">

      <!-- tombol spin: BUKAN submit -->
      <button class="btn btn-primary" type="button" id="btnRoll">
        Gacha Sekarang
      </button>

      <!-- tombol lain tetap submit normal -->
      <button class="btn wood" name="reset" value="1" type="submit">
        Reset Token
      </button>

      <a class="btn wood" href="pets.php">
        Lihat Slot
      </a>
    </form>


      <?php if ($rolled): ?>
        <div class="card" style="width:100%; max-width:720px;">
          <div style="display:flex; justify-content:space-between; align-items:center; gap:10px;">
            <div class="h2">Kamu mendapatkan: <?= esc($rolled['name']) ?></div>
            <span class="badge gold"><?= esc($rolled['rarity']) ?></span>
          </div>

          <img
            src="<?= esc(BASE_URL . '/' . ltrim($rolled['image_path'],'/')) ?>"
            alt="<?= esc($rolled['name']) ?>"
            style="margin-top:10px; width:100%; height:220px; object-fit:cover; border-radius:12px;"
          >

          <form method="post" style="margin-top:12px;">
            <button class="btn btn-primary" name="use" value="1" type="submit">Pakai Pet Ini</button>
          </form>


          <div class="small" style="margin-top:8px;">
            Kalau slot kosong, otomatis masuk slot pertama yang tersedia.
          </div>
        </div>
      <?php endif; ?>

    </div>
  </div>
</div>

<script>
const btnRoll = document.getElementById('btnRoll');
const wheel   = document.getElementById('wheel');
const form    = document.getElementById('gachaForm');

if (btnRoll && wheel && form) {
  btnRoll.addEventListener('click', function () {
    const spins = 4 + Math.random() * 3;
    const deg = 360 * spins + Math.floor(Math.random() * 360);

    wheel.style.transition = 'transform 1.2s cubic-bezier(.1,.8,.2,1)';
    wheel.style.transform = `rotate(${deg}deg)`;

    // submit setelah animasi
    setTimeout(() => {
      form.submit();
    }, 1250);
  });
}
</script>
  

<?php
render_footer();
