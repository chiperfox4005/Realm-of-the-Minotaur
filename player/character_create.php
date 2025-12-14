<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/layout.php';

$u = require_login();
if (($u['role'] ?? '') === 'admin') redirect(BASE_URL . '/admin/pets.php');

$char = get_character((int)$u['id']);
if ($char) redirect(BASE_URL . '/player/dashboard.php');

start_session();
$step = (int)($_POST['step'] ?? 0);

/** STEP 1 SUBMIT */
if ($step === 1) {
  $_SESSION['draft_name'] = trim($_POST['name'] ?? '');
  redirect(BASE_URL . '/player/character_create.php');
}

/** STEP 2 SUBMIT */
if ($step === 2) {
  $_SESSION['draft_gender'] = (string)($_POST['gender'] ?? '');
  redirect(BASE_URL . '/player/character_create.php');
}

/** STEP 3 SUBMIT */
if ($step === 3) {
  $name   = trim($_SESSION['draft_name'] ?? '');
  $gender = (string)($_SESSION['draft_gender'] ?? '');
  $ability= (string)($_POST['ability'] ?? '');

  $allowedG = ['Cowok','Cewek'];
  $allowedA = ['Attacker','Defender','Healing'];

  if ($name === '' || !in_array($gender, $allowedG, true) || !in_array($ability, $allowedA, true)) {
    unset($_SESSION['draft_name'], $_SESSION['draft_gender']);
    redirect(BASE_URL . '/player/character_create.php');
  }

  db()->prepare("INSERT INTO characters (user_id, name, gender, ability) VALUES (?, ?, ?, ?)")
     ->execute([(int)$u['id'], $name, $gender, $ability]);

  unset($_SESSION['draft_name'], $_SESSION['draft_gender']);
  redirect(BASE_URL . '/player/dashboard.php');
}

$nameDraft   = trim((string)($_SESSION['draft_name'] ?? ''));
$genderDraft = (string)($_SESSION['draft_gender'] ?? '');


render_header('Buat Karakter', 'bg-tavern');
?>

<div class="center-container">
  <div class="panel character-panel">
    <?php if ($nameDraft === ''): ?>
      <h2 class="character-title">Masukkan Nama Karakter</h2>

      <form method="post">
        <input type="hidden" name="step" value="1">

        <div class="form-group">
          <label for="name">Nama Karakter</label>
          <input id="name" name="name" required placeholder="Nama karakter..." class="character-input">
        </div>

        <button class="btn btn-primary btn-block character-btn" type="submit">Lanjut</button>
      </form>

    <?php elseif ($genderDraft === ''): ?>
      <h2 class="character-title">Pilih Gender</h2>
  <p class="character-subtitle">Nama: <b><?= esc($nameDraft) ?></b></p>

  <form method="post">
    <input type="hidden" name="step" value="2">

    <div class="grid grid-2 character-grid">
      <!-- COWOK -->
      <label class="card character-choice gender-card">
        <input class="choice-radio" type="radio" name="gender" value="Cowok" required>

        <div class="gender-header">
          <div class="gender-name">Cowok</div>
        </div>

        <div class="gender-previews">
          <img src="<?= BASE_URL ?>/image/cowok.jpg" alt="Cowok">
        </div>
      </label>

      <!-- CEWEK -->
      <label class="card character-choice gender-card">
        <input class="choice-radio" type="radio" name="gender" value="Cewek" required>

        <div class="gender-header">
          <div class="gender-name">Cewek</div>
        </div>

        <div class="gender-previews">
          <img src="<?= BASE_URL ?>/image/cewek.jpg" alt="Cewek">
        </div>
      </label>
    </div>

    <button class="btn btn-primary btn-block character-btn mt-2" type="submit">Lanjut</button>
  </form>

    <?php else: ?>
      <h2 class="character-title">Pilih Ability</h2>
  <p class="character-subtitle">
    Nama: <b><?= esc($nameDraft) ?></b> • Gender: <b><?= esc($genderDraft) ?></b>
  </p>

  <form method="post">
    <input type="hidden" name="step" value="3">

    <div class="ability-grid">


      <label class="card ability-card">
        <input class="ability-radio" type="radio" name="ability" value="Defender" required>
        <div class="ability-content">
          <div class="ability-title">Defender</div>
          <div class="ability-desc">
            Pertahanan kuat, cocok untuk bertahan di garis depan.
          </div>
        </div>
      </label>

      <label class="card ability-card">
        <input class="ability-radio" type="radio" name="ability" value="Attacker" required>
        <div class="ability-content">
          <div class="ability-title">Attacker</div>
          <div class="ability-desc">
            Serangan tinggi, fokus menghabisi boss lebih cepat.
          </div>
        </div>
      </label>

      <label class="card ability-card">
        <input class="ability-radio" type="radio" name="ability" value="Healing" required>
        <div class="ability-content">
          <div class="ability-title">Healing</div>
          <div class="ability-desc">
            Memulihkan HP, cocok untuk pertarungan panjang.
          </div>
        </div>
      </label>

    </div>

    <button class="btn btn-primary btn-block character-btn mt-2" type="submit">
      Selesai
    </button>
  </form>
    <?php endif; ?>

  </div>
</div>

<?php render_footer(); ?>
