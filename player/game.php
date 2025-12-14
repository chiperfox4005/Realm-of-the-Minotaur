<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/layout.php';

$u = require_login();
if (($u['role'] ?? '') === 'admin') redirect(BASE_URL . '/admin/pets.php');

$char = get_character((int)$u['id']);
if (!$char) redirect(BASE_URL . '/player/character_create.php');

start_session();

$heroImg = ($char['gender'] === 'Cewek') ? 'image/cewek.jpg' : 'image/cowok.jpg';
$bossImg = 'image/bos iblis.jpg';

$st = db()->prepare("
  SELECT p.name, p.image_path
  FROM user_pet_slots s
  JOIN user_pets up ON up.id = s.user_pet_id
  JOIN pets p ON p.id = up.pet_id
  WHERE s.user_id=? AND s.slot_no=1
");
$st->execute([(int)$u['id']]);
$pet = $st->fetch();

$heroSrc = asset_url($heroImg);
$bossSrc = asset_url($bossImg);
$petSrc  = $pet ? asset_url((string)$pet['image_path']) : '';


if (!isset($_SESSION['game_started'])) {
  $_SESSION['game_started'] = true;
  $_SESSION['hp_player'] = 8;
  $_SESSION['hp_boss'] = 15;
  $_SESSION['ability_left'] = 3;
  $_SESSION['turn'] = 0;
}

$questions = [
  ["q"=>"Kamu melihat jejak darah di tanah...", "A"=>["Ikuti jejak", 0, -1], "B"=>["Putar arah", -1, 0]],
  ["q"=>"Boss menyerang dengan api!", "A"=>["Menghindar", 0, -1], "B"=>["Menahan", -2, 0]],
  ["q"=>"Boss memanggil bayangan!", "A"=>["Serang duluan", -1, -2], "B"=>["Bertahan", 0, -1]],
  ["q"=>"Serangan mental!", "A"=>["Fokus", 0, -1], "B"=>["Panik", -1, 0]],
  ["q"=>"Boss charge pamungkas!", "A"=>["Pet ability", 0, -4], "B"=>["Rush", -3, 0]],
];

$msg = '';
$over = false;
$result = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['reset'])) {
    unset($_SESSION['game_started'], $_SESSION['hp_player'], $_SESSION['hp_boss'], $_SESSION['ability_left'], $_SESSION['turn']);
    redirect(BASE_URL . '/player/game.php');
  }

  if (isset($_POST['pet_ability'])) {
    if (!$pet) $msg = "Kamu belum punya pet aktif.";
    else if ($_SESSION['ability_left'] <= 0) $msg = "Ability habis.";
    else {
      $_SESSION['ability_left']--;
      $_SESSION['hp_boss'] -= 3;
      $msg = "Pet menggunakan ability! Boss -3 HP.";
    }
  }

  if (isset($_POST['choice'])) {
    $t = (int)$_SESSION['turn'];
    if (isset($questions[$t])) {
      $c = $_POST['choice'];
      [$text, $dp, $db] = $questions[$t][$c];
      $_SESSION['hp_player'] += $dp;
      $_SESSION['hp_boss'] += $db;
      $_SESSION['turn']++;
      $msg = "Kamu memilih: $text";
    }
  }

  if ($_SESSION['hp_player'] <= 0 && $_SESSION['hp_boss'] <= 0) { $over=true; $result="Keduanya tumbang... KAMU KALAH"; }
  elseif ($_SESSION['hp_player'] <= 0) { $over=true; $result="Kesatria jatuh... KAMU KALAH"; }
  elseif ($_SESSION['hp_boss'] <= 0) { $over=true; $result="Boss roboh! KAMU MENANG"; }

  if ($over) $_SESSION['game_started'] = false;
}

render_header('Game');
?>
<div class="container">
  <div class="panel">
    <div class="h1">Pertarungan Final</div>
    <div class="muted">HP Player: <b><?= (int)$_SESSION['hp_player'] ?></b> | HP Boss: <b><?= (int)$_SESSION['hp_boss'] ?></b> | Ability: <b><?= (int)$_SESSION['ability_left'] ?></b></div>
    <hr class="sep">

    <?php if ($msg): ?><div class="msg"><?= esc($msg) ?></div><?php endif; ?>

    <div class="game-grid">
      <div class="card game-card">
        <div class="h2">Kesatria</div>
        <img class="game-img" src="<?= esc($heroSrc) ?>" alt="hero">
        <div class="small">Ability: <b><?= esc($char['ability']) ?></b></div>
      </div>

      <div class="card game-card">
        <div class="h2">Boss</div>
        <img class="game-img" src="<?= esc($bossSrc) ?>" alt="boss">
      </div>

      <div class="card game-card">
        <div class="h2">Pet (Slot 1)</div>

        <?php if ($pet): ?>
          <img class="game-img" src="<?= esc($petSrc) ?>" alt="pet">
          <div class="small"><b><?= esc($pet['name']) ?></b></div>
        <?php else: ?>
          <div class="muted">Belum ada pet aktif.</div>
        <?php endif; ?>

        <form method="post" class="game-actions">
          <button class="btn" type="submit" name="pet_ability" value="1" <?= $pet ? '' : 'disabled' ?>>
            Gunakan Ability
          </button>
        </form>
      </div>
    </div>


    <hr class="sep">

    <?php if ($over): ?>
      <div class="h2"><?= esc($result) ?></div>
      <form method="post" style="margin-top:10px;">
        <button class="btn" name="reset" value="1" type="submit">Main Lagi</button>
        <a class="btn wood" href="dashboard.php">Kembali</a>
      </form>
    <?php else: ?>
      <?php $t=(int)$_SESSION['turn']; ?>
      <?php if (isset($questions[$t])): ?>
        <div class="h2"><?= esc($questions[$t]['q']) ?></div>
        <form method="post" style="margin-top:10px; display:flex; gap:10px; flex-wrap:wrap;">
          <button class="btn" name="choice" value="A" type="submit">A: <?= esc($questions[$t]['A'][0]) ?></button>
          <button class="btn danger" name="choice" value="B" type="submit">B: <?= esc($questions[$t]['B'][0]) ?></button>
        </form>
      <?php else: ?>
        <div class="muted">Pertanyaan habis, tapi boss masih kuat. Reset untuk main ulang.</div>
        <form method="post" style="margin-top:10px;">
          <button class="btn" name="reset" value="1" type="submit">Reset</button>
        </form>
      <?php endif; ?>
    <?php endif; ?>

  </div>
</div>
<?php render_footer(); ?>
