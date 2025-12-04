<?php
// dashboard.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Data dari halaman login
$nama    = $_SESSION['nama']    ?? 'Player';
$gender  = $_SESSION['gender']  ?? 'Belum memilih';
$ability = $_SESSION['ability'] ?? 'Belum memilih';

// Nama hewan versi tetap (tidak bisa diubah)
$petNames = [
    1 => "Minothorn Emberjaw",
    2 => "Frostmane Auroxveil",
    3 => "Nocthorn Dreadcaller",
    4 => "Vinehorn Orchardbane",
    5 => "Oxwald the Plain"
];

// Ability hewan (misterius + lawakan Jawa)
$petDesc = [
    1 => "Elemen: Api
Rarity: S
Gaya Medieval: Gladiator api dari Forge of Old Kings
Jurus Utama: Blazing Ox Requiem
Efek:
🔥 Damage +45%
🔥 Musuh terbakar 3 detik.",

    2 => "Elemen: Es
Rarity: A
Gaya Medieval: Ksatria es penjaga Frost Keep
Jurus Utama: Winterbull Dominion
Efek:
❄️ Membekukan musuh 2 detik
❄️ Defense +30%.",

    3 => "Elemen: Kegelapan
Rarity: SS
Gaya Medieval: Necromancer bull-lord
Jurus Utama: Abyssal Hoof Cataclysm
Efek:
🌑 Damage shadow +45%
🌑 Memanggil roh 'Banteng Tanpa Pajak' (summon).",

    4 => "Elemen: Buah-Buahan / Alam
Rarity: B
Gaya Medieval: Guardian of Forbidden Orchard
Jurus Utama: Fruitstorm Siege
Efek:
🍏 Memanggil badai buah keras (damage area +30%)
🍉 Heal +20% karena unsur alam.",

    5 => "Elemen: Tidak ada
Rarity: F (Hewan sampah favorit player)
Gaya Medieval: Petani medieval
Jurus Utama: Nothing Strike
Efek:
🥛 Keberuntungan +45% (aneh tapi nyata)
🥛 Damage 0, tapi bikin musuh bingung (mental damage)."
];

// PATH GAMBAR
$petImgs = [
    1 => '/MINOTAUR/image/pet1.jpg',
    2 => '/MINOTAUR/image/pet2.jpg',
    3 => '/MINOTAUR/image/pet3.jpg',
    4 => '/MINOTAUR/image/pet4.jpg',
    5 => '/MINOTAUR/image/pet5.jpg'
];

// PATH GAMBAR
$petImgs = [
    1 => '/MINOTAUR/image/banteng api.jpg',
    2 => '/MINOTAUR/image/banteng es.jpg',
    3 => '/MINOTAUR/image/banteng emas.jpg',
    4 => '/MINOTAUR/image/banteng buah.jpg',
    5 => '/MINOTAUR/image/bateng biasa.jpg'
];

$bossImg = '/MINOTAUR/image/bos iblis.jpg';

// DESKRIPSI ABILITY PLAYER (disederhanakan)
$abilityDesc = [
    "Attacker" => "Mengandalkan serangan kuat dan langsung ke sasaran.",
    "Defender" => "Tahan pukulan dan menjaga diri dengan pertahanan kokoh.",
    "Healing"  => "Mampu memulihkan kondisi saat pertarungan berlangsung.",
    "Belum memilih" => "Belum memilih ability."
];
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Dashboard - Realm of the Minotaur</title>
  <link rel="stylesheet" href="/MINOTAUR/dashboard/style.css">
</head>

<body>

  <!-- NAVBAR -->
  <nav class="navbar">
    <div class="nav-logo">Realm of the Minotaur</div>
    <ul class="nav-menu">
      <li><a href="/MINOTAUR/dashboard/profile.php">Profile</a></li>
      <li><a href="/MINOTAUR/dashboard/Hewan_peliharaan.php">Hewan Peliharaan</a></li>
      <li><a href="/MINOTAUR/dashboard/Gacha.php">Gacha</a></li>
      <li><a href="/MINOTAUR/dashboard/game.php">Game</a></li>
    </ul>
  </nav>

  <!-- PAGE -->
  <main class="page-wrap">
    <!-- CHARACTER BANNER -->
  <section class="character-banner">
  <img src="/MINOTAUR/image/sahabat.png" alt="Character Banner">
  </section>


    <!-- INFO PLAYER -->
    <section class="ability-box">
      <h2>Informasi Player</h2>
      <div class="ability-inner">
        <div><strong>Nama:</strong> <?= htmlspecialchars($nama) ?></div>
        <div><strong>Gender:</strong> <?= htmlspecialchars($gender) ?></div>
        <div><strong>Ability:</strong> <?= htmlspecialchars($ability) ?></div>
      </div>
    </section>

    <!-- PETS DAN ABILITY -->
    <section class="pets-and-info">

      <!-- INFORMASI HEWAN (NON-EDITABLE) -->
      <div class="left-col">
        <div class="info-hewan">
          <h3 class="info-hewan-title">Hewan misterius yang hanya muncul untuk pemain tertentu.</h3>
          <p>Kumpulan makhluk yang jarang terlihat oleh petualang biasa.</p>
        </div>

        <div class="pet-grid-left">
          <?php for ($i = 1; $i <= 5; $i++): ?>
            <article class="pet-card">
              <img src="<?= $petImgs[$i] ?>" alt="Pet <?= $i ?>">
              <div class="pet-info">
                <h3><?= htmlspecialchars($petNames[$i]) ?></h3>
                <p><?= htmlspecialchars($petDesc[$i]) ?></p>
              </div>
            </article>
          <?php endfor; ?>
        </div>
      </div>

      <!-- INFO ABILITY -->
      <aside class="right-col">
        <div class="ability-summary">
          <h3>Informasi Ability</h3>
          <p><strong><?= htmlspecialchars($nama) ?></strong> memiliki kemampuan:</p>

          <div class="ability-card">
            <h4><?= htmlspecialchars($ability) ?></h4>
            <p><?= $abilityDesc[$ability] ?></p>
          </div>
        </div>
      </aside>

    </section>

    <!-- BOSS -->
    <section class="boss-section">
      <div class="section-title">Informasi Boss</div>
      <div class="section-subtitle">Siapkan dirimu sebelum memasuki pertarungan final.</div>

      <div class="boss-card">
        <div class="boss-left">
          <img src="<?= $bossImg ?>" alt="Boss" class="boss-img">
        </div>

        <div class="boss-right">
          <h2>The God of Boss</h2>
          <p>Sosok kuat yang menguji seluruh kemampuanmu.</p>
          <ul>
            <li>Level minimal: 15</li>
            <li>Hewan peliharaan: Minimal Rare</li>
            <li>Ability yang direkomendasikan: Attacker / Defender</li>
            <li>Senjata: Pedang Mistis</li>
          </ul>
        </div>
      </div>
    </section>

  </main>

</body>
</html>
