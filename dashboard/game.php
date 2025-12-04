<?php
// game.php - Full PHP (no JS) battle vs Iblis, 6 soal (dari user)
session_start();

// ----- RESET VIA URL ?reset=1 -----
if (isset($_GET['reset']) && $_GET['reset'] == '1') {
    session_unset();
    session_destroy();
    header("Location: game.php");
    exit;
}

// ----- PERTANYAAN (user-provided) -----
$questions = [
    [
        "question" => "Iblis menembakkan api hitam ke arahmu!",
        "A" => ["text" => "Berguling menghindar.", "player" => 0, "boss" => -2],
        "B" => ["text" => "Menghalau dengan tangan kosong.", "player" => -2, "boss" => 0]
    ],
    [
        "question" => "Iblis memanggil roh kegelapan!",
        "A" => ["text" => "Pet-mu menerkam roh itu.", "player" => 0, "boss" => -3],
        "B" => ["text" => "Mundur ketakutan.", "player" => -1, "boss" => 0]
    ],
    [
        "question" => "Iblis menyerang dengan pedang bayangan!",
        "A" => ["text" => "Menangkis dengan bertahan.", "player" => 0, "boss" => -2],
        "B" => ["text" => "Menyerang maju tanpa perhitungan.", "player" => -2, "boss" => 0]
    ],
    [
        "question" => "Tanah retak! Ledakan neraka muncul!",
        "A" => ["text" => "Melompat mundur.", "player" => 0, "boss" => -1],
        "B" => ["text" => "Diam di tempat.", "player" => -2, "boss" => 0]
    ],
    [
        "question" => "Iblis berteriak, serangan mental!",
        "A" => ["text" => "Menutup telinga dan fokus.", "player" => 0, "boss" => -1],
        "B" => ["text" => "Panik.", "player" => -1, "boss" => 0]
    ],
    [
        "question" => "Iblis mengumpulkan energi untuk serangan pamungkas!",
        "A" => ["text" => "Pet mengeluarkan ability!", "player" => 0, "boss" => -4],
        "B" => ["text" => "Berlari maju!", "player" => -3, "boss" => 0]
    ]
];

// ----- INIT GAME STATE (if first time) -----
if (!isset($_SESSION['started'])) {
    $_SESSION['started'] = true;
    $_SESSION['hpPlayer'] = 8;      // HP Kesatria
    $_SESSION['hpBoss'] = 15;      // HP Boss
    $_SESSION['ability'] = 3;      // jumlah ability pet
    $_SESSION['index'] = 0;        // soal sekarang (0-based)

    // default image choices (bisa diset dari dashboard/choose page)
    $_SESSION['gender'] = $_SESSION['gender'] ?? 'cowok'; // 'cowok' or 'cewek'
    $_SESSION['pet'] = $_SESSION['pet'] ?? 'bateng biasa'; // nama file (sesuaikan file di folder)
}

// ----- Paths gambar (sesuaikan nama file di folder /MINOTAUR/image/) -----
$imgBoss = '/MINOTAUR/image/bos iblis.jpg';

// hero images (pastikan file ada)
$imgHero = ($_SESSION['gender'] === 'cewek')
    ? '/MINOTAUR/image/ksatria_cewek.png'
    : '/MINOTAUR/image/ksatria_cowok.png';

// pet image (nama sesuai session 'pet', kalau ada spasi gunakan path yang benar)
$petFile = str_replace(' ', '%20', $_SESSION['pet']); // encode spasi untuk safety
$imgPet = "/MINOTAUR/image/{$petFile}.jpg"; // contoh format: "/MINOTAUR/image/bateng%20biasa.jpg"
// jika file punya ekstensi png ubah sesuai kebutuhan

// ----- HANDLE ACTIONS (choice / ability) -----
$gameMessage = null;
$gameOver = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // use ability
    if (isset($_POST['ability'])) {
        if ($_SESSION['ability'] > 0) {
            $_SESSION['ability']--;
            // ability damage -3 by default; but question 6 A does -4 (we'll keep default -3 here)
            $_SESSION['hpBoss'] += -3;
            $gameMessage = "Hewanmu menggunakan ability! Bos -3 HP.";
        } else {
            $gameMessage = "Ability habis!";
        }
    }

    // choice A/B
    if (isset($_POST['choice'])) {
        $choice = $_POST['choice']; // 'A' atau 'B'
        $idx = $_SESSION['index'];
        // safety: cek index valid
        if (isset($questions[$idx])) {
            $opt = $questions[$idx][$choice];

            // Apply values: note values in array are already negatives for damage to boss/player
            // e.g. 'player' => -2 means decrease hpPlayer by 2 => hpPlayer += (-2)
            $_SESSION['hpPlayer'] += $opt['player'];
            $_SESSION['hpBoss'] += $opt['boss'];

            $gameMessage = "Kamu memilih: " . $opt['text'];
        } else {
            $gameMessage = "Tidak ada pertanyaan lagi.";
        }
    }

    // after action, check win/lose rules:
    // - if both <= 0 => player loses (per rule)
    // - else if player <= 0 => lose
    // - else if boss <= 0 => win
    if ($_SESSION['hpPlayer'] <= 0 && $_SESSION['hpBoss'] <= 0) {
        $resultText = "Kedua pihak tumbang... <strong>KAMU KALAH</strong>";
        $gameOver = true;
    } elseif ($_SESSION['hpPlayer'] <= 0) {
        $resultText = "Kesatria jatuh... <strong>KAMU KALAH</strong>";
        $gameOver = true;
    } elseif ($_SESSION['hpBoss'] <= 0) {
        $resultText = "Boss iblis roboh! <strong>KAMU MENANG</strong>";
        $gameOver = true;
    } else {
        // advance question index only when a choice was made (not ability)
        if (isset($_POST['choice'])) {
            $_SESSION['index']++;
        }
        // if we've exhausted all questions and boss still alive => fail
        if ($_SESSION['index'] >= count($questions)) {
            $resultText = "Cerita selesai, namun iblis masih berdiri... <strong>KAMU GAGAL</strong>";
            $gameOver = true;
        }
    }

    if ($gameOver) {
        // don't destroy session blindly; keep result and provide restart link
        $finalMessage = $resultText;
    }
}
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Pertarungan Kesatria vs Iblis — Minotaur</title>
<style>
   {flex-direction:column} .hpbar{width:160px} .card{min-width:140px} .choices{flex-direction:column} }
</style>
</head>
<body>
<div class="wrap">
    <header>
        <h1>⚔️ Pertarungan: Kesatria & Minotaur VS Azrakor (Iblis)</h1>
        <div class="small">Mode: Cerita (full PHP) — Soal tersisa: <?= max(0, count($questions) - $_SESSION['index']) ?></div>
    </header>

    <div class="arena">
        <div class="card">
            <h3>Kesatria</h3>
            <img src="<?= htmlspecialchars($imgHero) ?>" alt="Kesatria" width="140" height="140">
            <div class="small">Hewan partner</div>
            <img src="<?= htmlspecialchars($imgPet) ?>" alt="Pet" width="120" height="100" style="margin-top:6px">
            <div class="small" style="margin-top:6px"><?= htmlspecialchars(ucfirst($_SESSION['pet'])) ?></div>
        </div>

        <div class="card" style="flex:1">
            <h3 style="color:var(--accent)">Arena Pertarungan</h3>

            <?php if (!empty($gameMessage)): ?>
                <div class="msg"><?= htmlspecialchars($gameMessage) ?></div>
            <?php endif; ?>

            <?php if (isset($finalMessage)): ?>
                <div class="result" style="margin-top:12px"><?= $finalMessage ?></div>
            <?php endif; ?>

            <div class="status" style="margin-top:12px">
                <div>
                    <div class="small">❤️ Kesatria</div>
                    <div class="hpbar" title="HP Kesatria <?= max(0, $_SESSION['hpPlayer']) ?>">
                        <?php
                        $pPct = max(0, min(100, round( ($_SESSION['hpPlayer'] / 8) * 100 )));
                        ?>
                        <div class="hpfill" style="width:<?= $pPct ?>%;background:linear-gradient(90deg,#7de27a, #f6d24a, #f05d3d)"></div>
                    </div>
                    <div class="small"><?= max(0, $_SESSION['hpPlayer']) ?> / 8 HP</div>
                </div>

                <div>
                    <div class="small">👹 Azrakor (Boss)</div>
                    <div class="hpbar" title="HP Boss <?= max(0, $_SESSION['hpBoss']) ?>">
                        <?php
                        $bPct = max(0, min(100, round( ($_SESSION['hpBoss'] / 15) * 100 )));
                        ?>
                        <div class="hpfill" style="width:<?= $bPct ?>%;background:linear-gradient(90deg,#ffb347,#ff6b6b)"></div>
                    </div>
                    <div class="small"><?= max(0, $_SESSION['hpBoss']) ?> / 15 HP</div>
                </div>
            </div>

            <div class="question">
                <?php if (!isset($finalMessage)): ?>
                    <h4>Pertanyaan <?= $_SESSION['index'] + 1 ?></h4>
                    <p><?= htmlspecialchars($questions[$_SESSION['index']]['question']) ?></p>

                    <div class="choices">
                        <form method="post" style="display:inline-block;margin-right:8px">
                            <input type="hidden" name="choice" value="A">
                            <button class="btn" type="submit">A &mdash; <?= htmlspecialchars($questions[$_SESSION['index']]['A']['text']) ?></button>
                        </form>

                        <form method="post" style="display:inline-block;margin-left:8px">
                            <input type="hidden" name="choice" value="B">
                            <button class="btn" type="submit">B &mdash; <?= htmlspecialchars($questions[$_SESSION['index']]['B']['text']) ?></button>
                        </form>
                    </div>

                    <div style="margin:14px 0">
                        <form method="post" style="display:inline-block">
                            <button class="ability" type="submit" name="ability">🔥 Gunakan Ability Pet (<?= intval($_SESSION['ability']) ?> tersisa)</button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>

        </div>

        <div class="card">
            <h3>Boss Iblis</h3>
            <img src="<?= htmlspecialchars($imgBoss) ?>" alt="Boss" width="220" height="160">
            <div class="small" style="margin-top:6px">Nama: Azrakor — Penguasa Neraka</div>
        </div>
    </div>

    <div class="footer">
        <div>
            <a class="link" href="/MINOTAUR/dashboard/dashboard.php">⬅ Kembali ke Dashboard</a>
        </div>
        <div>
            <a class="link" href="?reset=1">Restart Game</a>
        </div>
    </div>

    <div style="margin-top:12px;font-size:13px;color:#c9b08a">
        Tip: Pilih A untuk opsi aman/serangan, B kadang jebakan. Gunakan ability untuk serangan besar.
    </div>
</div>
</body>
</html>
