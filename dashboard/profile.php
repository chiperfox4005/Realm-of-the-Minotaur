<?php
session_start();

// Data dari login
$nama   = $_SESSION['nama']   ?? "Player";
$gender = $_SESSION['gender'] ?? "cowok";

// ==================================================================
//       AMBIL PET AKTIF DARI SISTEM 2 SLOT HEWAN PELIHARAAN
// ==================================================================
$activePet = null;

if (isset($_SESSION['pets'][1]) && $_SESSION['pets'][1] !== null) {
    $activePet = $_SESSION['pets'][1];
} elseif (isset($_SESSION['pets'][2]) && $_SESSION['pets'][2] !== null) {
    $activePet = $_SESSION['pets'][2];
}

// Tentukan gambar hero berdasarkan gender
$imgHero = ($gender === 'cewek')
            ? "/MINOTAUR/image/cewek.jpg"
            : "/MINOTAUR/image/cowok.jpg";

// Data 5 hewan dari dashboard
$petNames = [
    1 => "Minothorn Emberjaw",
    2 => "Frostmane Auroxveil",
    3 => "Nocthorn Dreadcaller",
    4 => "Vinehorn Orchardbane",
    5 => "Oxwald the Plain"
];

$petOrigins = [
    1 => "Terlahir dari inti kawah gunung berapi kuno. Tanduknya mengandung bara abadi.",
    2 => "Penjaga Gerbang Es Utara. Nafasnya dapat membekukan waktu sesaat.",
    3 => "Makhluk dari jurang kegelapan. Mengendalikan roh banteng yang tak pernah tidur.",
    4 => "Pelindung kebun terlarang. Tubuhnya dipenuhi energi alam yang menyembuhkan.",
    5 => "Banteng biasa yang tersesat, tetapi beruntung memiliki jiwa petarung sejati."
];

$petImgs = [
    1 => '/MINOTAUR/image/banteng api.jpg',
    2 => '/MINOTAUR/image/banteng es.jpg',
    3 => '/MINOTAUR/image/banteng emas.jpg',
    4 => '/MINOTAUR/image/banteng buah.jpg',
    5 => '/MINOTAUR/image/bateng biasa.jpg'
];

$sebutanKesatria = ($gender === 'cewek') ? "Kesatria Perempuan" : "Kesatria Laki-laki";

?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>Profile — Realm of the Minotaur</title>
<style>
    .btn{
        padding:10px 15px;
        display:inline-block;
        background:#6e4829;
        border:2px solid #a77a4f;
        color:#f0e6d2;
        border-radius:8px;
        text-decoration:none;
        margin-right:8px;
        font-weight:bold;
    }
    
</style>
</head>
<body>

<div class="card">
    <h1>Profile <?= htmlspecialchars($nama) ?></h1>

    <div class="wrapper">

        <!-- GAMBAR KESATRIA -->
        <div class="portrait">
            <h3><?= $sebutanKesatria ?></h3>
            <img src="<?= $imgHero ?>" alt="Kesatria">

            <!-- PET DARI SLOT -->
            <div class="pet-box">
                <?php if ($activePet): ?>
                    <p>Hewan Pendamping:</p>
                    <img src="<?= $activePet['img'] ?>" alt="Pet">
                    <p><b><?= htmlspecialchars($activePet['nickname'] ?? $activePet['name']) ?></b></p>
                <?php else: ?>
                    <p><i>Belum memiliki pet</i></p>
                <?php endif; ?>
            </div>
        </div>

        <!-- CERITA -->
        <div class="story">
            <p>
                Di tanah utara yang diselimuti kabut abadi, hiduplah seorang kesatria bernama
                <b><?= htmlspecialchars($nama) ?></b>. Walau tak lahir dengan kekuatan besar,
                tekadnya tak pernah padam.
            </p>

            <p>
                Ketika pertama kali menantang Minotaur, tubuhnya terpental jauh hanya oleh satu pukulan.
                Kekalahan itu membuka matanya bahwa ia tak bisa berjuang sendirian.
            </p>

            <p>
                Sejak hari itu, <?= htmlspecialchars($nama) ?> memulai perjalanan panjang untuk mencari
                hewan pendamping yang dapat memperkuatnya, sekaligus menjadi sahabat dalam pertempuran.
            </p>

            <p>
                Inilah awal dari legenda sang kesatria. Dengan hewan pendamping di sisinya,
                hari itu pasti tiba: hari ketika ia kembali menantang Minotaur.
            </p>

            <br>

            <a class="btn" href="/MINOTAUR/dashboard/game.php">Mulai Pertarungan</a>
            <a class="btn" href="/MINOTAUR/dashboard/dashboard.php">Kembali ke Dashboard</a>
        </div>
    </div>

    <!-- LORE HEWAN -->
    <h2 class="pet-lore-title">Asal Usul 5 Hewan Legendaris</h2>

    <div class="pet-lore-grid">
        <?php for ($i = 1; $i <= 5; $i++): ?>
        <div class="lore-card">
            <img src="<?= $petImgs[$i] ?>" alt="Pet <?= $i ?>">
            <h3><?= htmlspecialchars($petNames[$i]) ?></h3>
            <p><?= $petOrigins[$i] ?></p>
        </div>
        <?php endfor; ?>
    </div>

</div>

</body>
</html>
