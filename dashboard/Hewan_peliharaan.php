<?php
session_start();

// INIT SLOT (maks 2 pet)
if (!isset($_SESSION['pets'])) {
    $_SESSION['pets'] = [
        1 => null,
        2 => null
    ];
}

// JIKA ADA PET BARU DARI GACHA
if (isset($_SESSION['new_pet'])) {
    
    foreach ($_SESSION['pets'] as $slot => $pet) {
        
        if ($pet === null) {
            $_SESSION['pets'][$slot] = $_SESSION['new_pet'];
            unset($_SESSION['new_pet']);
            break;
        }
    }
}

// EDIT NAMA
if (isset($_POST['edit_pet'])) {
    $slot = $_POST['slot'];
    $newName = $_POST['nickname'];

    if (!empty($_SESSION['pets'][$slot])) {
        $_SESSION['pets'][$slot]['nickname'] = $newName;
    }
}

// HAPUS PET
if (isset($_POST['remove_pet'])) {
    $slot = $_POST['slot'];
    $_SESSION['pets'][$slot] = null;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Hewan Peliharaan</title>
    <style>
        body {
            background: #202020;
            color: white;
            font-family: Arial;
            padding: 20px;
        }
        h1 { text-align: center; }
        .pet-container {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 25px;
        }
        .card {
            background: #2d2d2d;
            width: 280px;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 0 10px black;
        }
        .pet-img {
            width: 180px;
            border-radius: 10px;
        }
        .btn {
            background: #ffcc00;
            border: none;
            padding: 10px 18px;
            border-radius: 8px;
            cursor: pointer;
            margin-top: 10px;
        }
        .btn:hover { background: #ffdd33; }
        .empty {
            opacity: 0.5;
        }
    </style>
</head>
<body>
<a href="/MINOTAUR/dashboard/dashboard.php">
    <button class="btn" style="
        background:#00aaff;
        margin-bottom: 20px;
        padding: 10px 20px;
        font-size: 16px;
        border-radius: 8px;
        cursor: pointer;
    ">⬅ Kembali ke Dashboard</button>
</a>

<h1>Hewan Peliharaanmu</h1>

<div class="pet-container">

    <!-- LOOP 2 SLOT -->
    <?php foreach ($_SESSION['pets'] as $slot => $pet): ?>
    <div class="card">

        <?php if ($pet === null): ?>
            <h2>Slot <?= $slot ?> Kosong</h2>
            <div class="empty">
                <img src="/MINOTAUR/image/empty.png" class="pet-img" alt="">
            </div>
            <br>
            <a href="/MINOTAUR/dashboard/Gacha.php">
                <button class="btn">Tambah dari Gacha</button>
            </a>

        <?php else: ?>
            <h2><?= $pet['nickname'] ?? $pet['name'] ?></h2>
            <img src="<?= $pet['img'] ?>" class="pet-img">

            <p><strong>Nama Asli:</strong> <?= $pet['name'] ?></p>

            <!-- Form Edit Nama Panggilan -->
            <form method="POST" style="margin-top: 15px;">
                <input type="hidden" name="slot" value="<?= $slot ?>">
                <input type="text" name="nickname" placeholder="Nama panggilan baru"
                       value="<?= $pet['nickname'] ?? '' ?>"
                       style="padding:7px; width: 90%; border-radius:5px;">
                <button type="submit" name="edit_pet" class="btn">Simpan</button>
            </form>

            <!-- Kucilkan Hewan -->
            <form method="POST">
                <input type="hidden" name="slot" value="<?= $slot ?>">
                <button type="submit" name="remove_pet" class="btn" style="background:#ff5555;">
                    Kucilkan Hewan
                </button>
            </form>

        <?php endif; ?>

    </div>
    <?php endforeach; ?>

</div>

</body>
</html>
