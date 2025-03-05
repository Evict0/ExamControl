<?php
session_start();

// Povezivanje na bazu (prilagodi po potrebi)
$servername = "localhost";
$dbUsername = "root";
$dbPassword = "";
$dbname     = "kviz2";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $dbUsername, $dbPassword);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Greška s bazom: " . $e->getMessage());
}

// Poruka za prikaz greške ili uspjeha
$poruka = "";

// Ako je forma za login poslana (POST), provjeri podatke
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $inputUsername = trim($_POST['username'] ?? '');
    $inputPassword = trim($_POST['password'] ?? '');

    if (empty($inputUsername) || empty($inputPassword)) {
        $poruka = "Molimo unesite korisničko ime i lozinku.";
    } else {
        // U SELECT-u dohvatimo i razinaID
        $sql = "SELECT ID, ime, lozinka, razinaID 
                FROM ep_korisnik 
                WHERE ime = :ime
                LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':ime', $inputUsername);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            // Postoji takav korisnik
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // U bazi je MD5 hash; usporedi s MD5 onoga što je unešeno
            if ($user['lozinka'] === md5($inputPassword)) {
                // Uspješna prijava -> postavi user_id i razinaID
                $_SESSION['user_id'] = $user['ID'];
                $_SESSION['razina']  = $user['razinaID'];

                header("Location: odabir_teme.php");
                exit();
            } else {
                $poruka = "Neispravna lozinka.";
            }
        } else {
            $poruka = "Korisnik ne postoji.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <title>Prijava</title>
    <link rel="stylesheet" href="style.css">
    <!-- Dodatno: stil za gumb 'Registracija' (isti kao 'Prijavi se') -->
    <style>
        .neon-button {
            background-color: #ff00ff;
            color: #fff;
            padding: 14px 28px;
            font-size: 1.1rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: 0.3s ease;
            margin-top: 10px;
            box-shadow: 0 0 5px #ff00ff, 0 0 10px #ff00ff;
        }
        .neon-button:hover {
            background-color: #d100d1;
            box-shadow: 0 0 10px #ff00ff, 0 0 20px #ff00ff;
        }
        .error {
            color: #ff2e2e;
            font-weight: bold;
            margin-top: 10px;
            text-shadow: 0 0 5px #ff2e2e;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Prijava</h2>

        <!-- Prikaz poruke (greška ili slično) -->
        <?php if (!empty($poruka)): ?>
            <p id="login-message" class="error"><?= htmlspecialchars($poruka) ?></p>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="username">Korisničko ime:</label>
                <input type="text" name="username" id="username">
            </div>

            <div class="form-group">
                <label for="password">Lozinka:</label>
                <input type="password" name="password" id="password">
            </div>

            <button type="submit" class="neon-button">Prijavi se</button>
        </form>

        <br>
        <!-- Gumb Registracija, izgleda jednako, vodi na registracija.php -->
        <a href="registracija.php" style="text-decoration:none;">
            <button type="button" class="neon-button">Registracija</button>
        </a>
    </div>
</body>
</html>
