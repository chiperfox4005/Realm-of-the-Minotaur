<?php
session_start();

function esc($s){ return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); }

$step = $_POST['step'] ?? null;

/* STEP 0: input nama */
if (!$step) {
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Masukkan Nama - MINOTAUR</title>
    <link rel="stylesheet" href="/MINOTAUR/Login_page/style.css">
</head>
<body>
<div class="login-container">
    <img src="/MINOTAUR/image/OIP.webp" alt="icon" class="character-img">

    <h2>Masukkan Nama Karakter</h2>

    <form method="post" action="">
        <input type="hidden" name="step" value="1">

        <label for="nama">Nama Karakter</label>
        <input id="nama" type="text" name="nama" required placeholder="Nama Karakter...">

        <button type="submit" class="btn-login">Lanjut</button>
    </form>
</div>
</body>
</html>
<?php
exit;
}


/* STEP 1: pilih gender */
if ($step === '1') {

    $_SESSION['nama'] = $_POST['nama'] ?? '';

    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Pilih Gender - MINOTAUR</title>
    <link rel="stylesheet" href="/MINOTAUR/Login_page/style.css">
</head>
<body>

<div class="login-container">
    <img id="previewGender" src="/MINOTAUR/image/cowok.jpg" class="gender-img">

    <h2>Pilih Gender</h2>

    <form method="post" action="">
        <input type="hidden" name="step" value="2">

        <div class="gender-flex">

            <label class="gender-box">
                <input type="radio" name="gender" value="Cowok" onclick="preview('cowok')" required>
                <span class="gender-label">Cowok</span>
            </label>

            <label class="gender-box">
                <input type="radio" name="gender" value="Cewek" onclick="preview('cewek')" required>
                <span class="gender-label">Cewek</span>
            </label>

        </div>

        <button type="submit" class="btn-login">Lanjut</button>
    </form>
</div>

<script>
function preview(g){
    let img = document.getElementById("previewGender");

    if(g === "cowok"){
        img.src = "/MINOTAUR/image/cowok.jpg";
    } else {
        img.src = "/MINOTAUR/image/cewek.jpg";
    }
}
</script>

</body>
</html>
<?php
exit;
}


/* STEP 2: pilih ability */
if ($step === '2') {

    $gender = $_POST['gender'] ?? '';
    $_SESSION['gender'] = $gender;

    $avatar = ($gender === "Cowok")
                ? "/MINOTAUR/image/cowok.jpg"
                : "/MINOTAUR/image/cewek.jpg";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Pilih Ability - MINOTAUR</title>
    <link rel="stylesheet" href="/MINOTAUR/Login_page/style.css">
</head>
<body>

<div class="login-container">
    <img src="<?= $avatar ?>" class="character-img">

    <h2>Pilih Ability</h2>

    <p><strong>Nama:</strong> <?= esc($_SESSION['nama']) ?></p>
    <p><strong>Gender:</strong> <?= esc($_SESSION['gender']) ?></p>

    <form method="post" action="">
        <input type="hidden" name="step" value="3">
        <label class="radio-square">
        <input type="radio" name="ability" value="Defender" required>
         <span>
        <strong>Defender</strong><br>
        <small>Defender memiliki perisai kuat seperti Burung gerindra yang melindungi rakyat +55%.</small>
        </span>
        </label>


        <label class="radio-square">
             <input type="radio" name="ability" value="Attacker" required>
        <span>
        <strong>Attacker</strong><br>
        <small>Attacker bisa menyerang secara brutal dengan  kekuatan spiritual yang berasal dari dewa pdip kuno +50.</small>
        </span>
        </label>

        <label class="radio-square">
         <input type="radio" name="ability" value="Healing" required>
         <span>
        <strong>Healing</strong><br>
        <small>Kekuatan penyembuhan tak terbatas dari Dewi PSI memberikan kesejahteraan hidup +45%.</small>
        </span>
        </label>

        <button type="submit" class="btn-login">Selesai</button>
    </form>
</div>

</body>
</html>
<?php
exit;
}


/* STEP 3: final */
if ($step === '3') {

    $_SESSION['ability'] = $_POST['ability'];

    $gender = $_SESSION['gender'];
    $final_img = ($gender === "Cowok")
                    ? "/MINOTAUR/image/cowok.jpg"
                    : "/MINOTAUR/image/cewek.jpg";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Karakter Jadi - MINOTAUR</title>
    <link rel="stylesheet" href="/MINOTAUR/Login_page/style.css">
</head>
<body>

<div class="login-container">
    <img src="<?= $final_img ?>" class="character-img">

    <h2>Karakter Berhasil Dibuat!</h2>

    <p><strong>Nama:</strong> <?= esc($_SESSION['nama']) ?></p>
    <p><strong>Gender:</strong> <?= esc($_SESSION['gender']) ?></p>
    <p><strong>Ability:</strong> <?= esc($_SESSION['ability']) ?></p>

    <div class="btn-container">
        <form method="post" action="">
            <button class="btn-login">Buat Baru</button>
        </form>

        <a href="/MINOTAUR/dashboard/dashboard.php" class="btn-login">Next</a>
    </div>
</div>

</body>
</html>
<?php
exit;
}
?>
