<?php
// gacha.php
session_start();

// TOKEN GACHA MAKSIMAL 3x
if (!isset($_SESSION['gacha_token'])) {
    $_SESSION['gacha_token'] = 2;
}

// DATA HEWAN
$gachaPool = [
    1 => "Minothorn Emberjaw",
    2 => "Frostmane Auroxveil",
    3 => "Nocthorn Dreadcaller",
    4 => "Vinehorn Orchardbane",
    5 => "Oxwald the Plain"
];

$gachaImages = [
    1 => '/MINOTAUR/image/banteng api.jpg',
    2 => '/MINOTAUR/image/banteng es.jpg',
    3 => '/MINOTAUR/image/banteng emas.jpg',
    4 => '/MINOTAUR/image/banteng buah.jpg',
    5 => '/MINOTAUR/image/bateng biasa.jpg'
];

$result = null;
$message = "";


// ▶ PROSES GACHA
if (isset($_POST['do_gacha'])) {
    if ($_SESSION['gacha_token'] > 0) {

        $_SESSION['gacha_token']--;

        $roll = rand(1, 5);

        $result = [
            'id'   => $roll,
            'name' => $gachaPool[$roll],
            'img'  => $gachaImages[$roll]
        ];

        $_SESSION['last_gacha'] = $result; // simpan untuk tombol "Pakai Pet"

    } else {
        $message = "Token gacha kamu sudah habis!";
    }
}

// ▶ RESET TOKEN
if (isset($_POST['reset_token'])) {
    $_SESSION['gacha_token'] = 2;
    $message = "Token berhasil di-reset!";
}

// ▶ SIMPAN PET KE FILE Hewan_peliharaan.php
if (isset($_POST['use_pet'])) {

    if (!isset($_SESSION['last_gacha'])) {
        $message = "Belum ada hasil gacha yang bisa dipakai.";
    } else {
        $_SESSION['pet_aktif'] = $_SESSION['last_gacha'];
        header("Location: /MINOTAUR/dashboard/Hewan_peliharaan.php");
        exit;
    }
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Gacha - Realm of the Minotaur</title>

    <style>
        body {
            background: #1b1b1b;
            color: #f5f5f5;
            font-family: Arial;
            text-align: center;
            padding: 30px;
        }
        .gacha-box {
            background: #2b2b2b;
            padding: 25px;
            max-width: 450px;
            margin: auto;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.4);
        }
        .btn {
            background: #ffcc00;
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            font-size: 18px;
            cursor: pointer;
            margin: 10px 5px;
        }
        .btn:hover {
            background: #ffdd33;
        }
        img {
            width: 250px;
            margin-top: 20px;
            border-radius: 10px;
        }
        .msg { margin-top: 15px; color: #ff5555; font-size: 18px; }
    </style>
</head>
<body>

<div class="gacha-box">
    <h2>Gacha Hewan Peliharaan</h2>
    <p>Sisa Token Gacha: <strong><?= $_SESSION['gacha_token'] ?></strong></p>

    <form method="POST">
        <button type="submit" name="do_gacha" class="btn">Gacha Sekarang</button>
        <button type="submit" name="reset_token" class="btn">Reset Token</button>
    </form>

    <?php if ($message): ?>
        <div class="msg"><?= $message ?></div>
    <?php endif; ?>

    <?php if ($result): ?>
        <h3>Kamu Mendapatkan:</h3>
        <img src="<?= $result['img'] ?>" alt="Hasil Gacha">
        <h2><?= $result['name'] ?></h2>

        <form method="POST">
            <button type="submit" name="use_pet" class="btn">Pakai Pet Ini</button>
        </form>
    <?php endif; ?>
</div>

</body>
</html>
