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

// Poruka (uspjeh / greška)
$poruka = "";

// Obrada registracije
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $ime = trim($_POST['ime'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $lozinka = trim($_POST['password'] ?? '');

    if (empty($ime) || empty($email) || empty($lozinka)) {
        $poruka = "Molimo ispunite sva polja.";
    } else {
        // Provjera postoji li već ovaj email
        $stmtCheck = $conn->prepare("SELECT ID FROM ep_korisnik WHERE email = :email LIMIT 1");
        $stmtCheck->bindParam(':email', $email);
        $stmtCheck->execute();

        if ($stmtCheck->rowCount() > 0) {
            $poruka = "Ovaj email je već registriran.";
        } else {
            // Dodaj novog korisnika (MD5 lozinka, razinaID=2 => učenik, aktivan=1)
            $stmtInsert = $conn->prepare("INSERT INTO ep_korisnik (ime, lozinka, razinaID, aktivan, email)
                                          VALUES (:ime, MD5(:lozinka), 2, 1, :email)");
            $stmtInsert->bindParam(':ime', $ime);
            $stmtInsert->bindParam(':lozinka', $lozinka);
            $stmtInsert->bindParam(':email', $email);
            $stmtInsert->execute();

            $poruka = "Uspješna registracija! Možete se prijaviti.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <title>Registracija</title>
    <link rel="stylesheet" href="style.css">
    <!-- Dodajemo isti stil za gumb kakav smo imali u loginu -->
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
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Registracija</h2>

        <!-- Poruka o uspjehu ili greški -->
        <?php if (!empty($poruka)) : ?>
            <p id="login-message"><?= htmlspecialchars($poruka) ?></p>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="ime">Ime ili korisničko ime:</label>
                <input type="text" name="ime" id="ime">
            </div>

            <div class="form-group">
                <label for="email">Email adresa:</label>
                <input type="text" name="email" id="email">
            </div>

            <div class="form-group">
                <label for="password">Lozinka:</label>
                <input type="password" name="password" id="password">
            </div>

            <button type="submit" class="neon-button">Registriraj se</button>
        </form>

        <br>
        <!-- Gumb 'Natrag na login' s jednakim stilom -->
        <a href="login.php" style="text-decoration:none;">
            <button type="button" class="neon-button">Natrag na login</button>
        </a>
    </div>
</body>
</html>
