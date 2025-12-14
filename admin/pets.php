<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../config/db.php';

require_admin();
$pdo = db();

$error = '';
$success = '';

function post(string $k): string {
  return trim((string)($_POST[$k] ?? ''));
}

/* DELETE */
if (isset($_GET['delete'])) {
  $id = (int)$_GET['delete'];
  if ($id > 0) {
    $pdo->prepare("DELETE FROM pets WHERE id=?")->execute([$id]);
    header("Location: pets.php");
    exit;
  }
}

/* CREATE */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_pet'])) {
  $code   = post('code');
  $name   = post('name');
  $element= post('element');
  $rarity = post('rarity');
  $image  = post('image_path');
  $lore   = post('lore');
  $weight = (int)($_POST['weight'] ?? 0);

  if ($code==='' || $name==='' || $rarity==='' || $image==='' || $lore==='' || $weight<=0) {
    $error = "Semua field wajib diisi.";
  } else {
    $st = $pdo->prepare("
      INSERT INTO pets (code, name, element, rarity, image_path, lore, weight)
      VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $st->execute([$code, $name, $element, $rarity, $image, $lore, $weight]);
    $success = "Companion berhasil ditambahkan.";
  }
}

$pets = $pdo->query("SELECT id, code, name, element, rarity, weight, image_path FROM pets ORDER BY id ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin - Companion Database</title>
  <link rel="stylesheet" href="../assets/css/styles.css">
  <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600;700&family=Cinzel+Decorative:wght@700;900&family=Lora&display=swap" rel="stylesheet">
</head>

<body class="bg-wood admin-page">
<nav class="nav">
  <div class="nav-container">
    <a href="../player/dashboard.php" class="nav-brand">Realm of the Minotaur</a>
    <ul class="nav-menu">
      <li><a href="../player/dashboard.php" class="nav-link">Kingdom</a></li>
      <li><a href="pets.php" class="nav-link active">Admin</a></li>
      <li><a href="../auth/logout.php" class="nav-link">Logout</a></li>
    </ul>
  </div>
</nav>

<div class="container">
  <div class="panel">
    <div class="panel-header text-center mb-3">
      <div class="panel-title">Companion Database</div>
      <p class="muted">Admin Control Panel</p>
    </div>

    <?php if ($error): ?>
      <div class="msg danger" style="margin-bottom:14px;"><?= esc($error) ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
      <div class="msg ok" style="margin-bottom:14px;"><?= esc($success) ?></div>
    <?php endif; ?>

    <!-- ADD PET -->
    <div class="card">
      <div class="card-header">
        <div class="card-title">Add New Companion</div>
      </div>

      <div class="card-body">
        <form method="POST" class="form">
          <input type="hidden" name="create_pet" value="1">

          <div class="grid grid-3">
            <div class="form-group">
              <label>Code</label>
              <input name="code" placeholder="ex: frost_ox" required>
            </div>

            <div class="form-group">
              <label>Name</label>
              <input name="name" placeholder="ex: Frostmane Auroxveil" required>
            </div>

            <div class="form-group">
              <label>Element</label>
              <input name="element" placeholder="Api / Es / Alam">
            </div>
          </div>

          <div class="grid grid-3">
            <div class="form-group">
              <label>Image Path</label>
              <input name="image_path" placeholder="image/banteng_api.jpg" required>
              <div class="small muted" style="margin-top:6px;">
                Pastikan file ada di folder yang benar (contoh: <b>public/image/...</b>).
              </div>
            </div>

            <div class="form-group">
              <label>Rarity</label>
              <select name="rarity" required>
                <option value="">Pilih rarity</option>
                <option>S</option>
                <option>A</option>
                <option>B</option>
                <option>C</option>
                <option>F</option>
              </select>
            </div>

            <div class="form-group">
              <label>Weight (Gacha)</label>
              <input type="number" name="weight" min="1" placeholder="ex: 20" required>
            </div>
          </div>

          <div class="form-group">
            <label>Lore / Deskripsi</label>
            <textarea name="lore" rows="3" placeholder="Tulis deskripsi singkat..." required></textarea>
          </div>

          <div style="display:flex; gap:10px; flex-wrap:wrap;">
            <button class="btn btn-primary" type="submit">Create Companion</button>
            <a class="btn" href="pets.php">Reset Form</a>
          </div>
        </form>
      </div>
    </div>

    <hr class="sep" style="margin:22px 0;">

    <!-- LIST -->
    <div class="card">
      <div class="card-header" style="display:flex; justify-content:space-between; align-items:center; gap:12px;">
        <div class="card-title">All Companions</div>
        <div class="badge">Total: <?= count($pets) ?></div>
      </div>

      <div class="card-body table-wrap">
        <table class="table">
          <thead>
            <tr>
              <th style="width:70px;">ID</th>
              <th>Code</th>
              <th>Name</th>
              <th style="width:110px;">Element</th>
              <th style="width:90px;">Rarity</th>
              <th style="width:90px;">Weight</th>
              <th style="width:220px;">Image</th>
              <th style="width:120px;">Action</th>
            </tr>
          </thead>
          <tbody>
          <?php foreach ($pets as $p): ?>
            <tr>
              <td><?= (int)$p['id'] ?></td>
              <td><?= esc($p['code']) ?></td>
              <td><?= esc($p['name']) ?></td>
              <td><?= esc($p['element'] ?? '-') ?></td>
              <td>
                <span class="badge badge-gold"><?= esc($p['rarity']) ?></span>
              </td>
              <td><?= (int)$p['weight'] ?></td>

              <td><?= esc($p['image_path']) ?></td>


              <td>
                <a class="btn btn-danger"
                   style="padding:.45rem .9rem;"
                   onclick="return confirm('Hapus companion ini?')"
                   href="?delete=<?= (int)$p['id'] ?>">
                  Delete
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>

        <?php if (count($pets) === 0): ?>
          <div class="muted" style="margin-top:12px;">Belum ada data companion.</div>
        <?php endif; ?>
      </div>
    </div>

  </div>
</div>

</body>
</html>
