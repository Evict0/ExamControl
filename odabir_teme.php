<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

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

// Get user info
$userId    = $_SESSION['user_id'];
$userLevel = $_SESSION['razina'] ?? 2; // 2 = student by default

// If teacher (razina == 1), get all topics; otherwise, only assigned ones
if ($userLevel == 1) {
    $stmt = $conn->prepare("
        SELECT t.ID AS theme_id, 
               t.naziv AS theme_name, 
               COUNT(p.ID) AS broj_pitanja
        FROM ep_teme t
        LEFT JOIN ep_pitanje p ON p.temaID = t.ID
        GROUP BY t.ID, t.naziv
        ORDER BY t.naziv
    ");
    $stmt->execute();
    $korisnikTeme = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $stmt = $conn->prepare("
        SELECT t.ID AS theme_id, 
               t.naziv AS theme_name, 
               COUNT(p.ID) AS broj_pitanja
        FROM ep_teme t
        INNER JOIN ep_korisnik_teme kt ON kt.tema_id = t.ID
        LEFT JOIN ep_pitanje p ON p.temaID = t.ID
        WHERE kt.korisnik_id = ?
        GROUP BY t.ID, t.naziv
        ORDER BY t.naziv
    ");
    $stmt->execute([$userId]);
    $korisnikTeme = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <title>Odabir teme</title>
    <style>
        /* Base reset and font */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        body {
            background: #000;
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        /* Container for the theme selection */
        .odabir-teme-container {
            width: 100%;
            max-width: 700px;
            background: linear-gradient(145deg, #111, #000);
            border: 2px solid #ff00ff;
            border-radius: 12px;
            box-shadow: 0 0 25px rgba(255, 0, 255, 0.2),
                        0 0 60px rgba(255, 0, 255, 0.1);
            padding: 30px;
            text-align: center;
        }
        .odabir-teme-container h2 {
            font-size: 2rem;
            font-weight: 600;
            color: #40ffe5;
            text-shadow: 0 0 8px #40ffe5, 0 0 20px #40ffe5;
            margin-bottom: 30px;
        }

        /* Vibrant "Dodaj novo pitanje" button for teachers */
        .teacher-link {
            display: inline-block;
            margin-bottom: 25px;
            padding: 16px 32px;
            font-size: 1.2rem;
            font-weight: bold;
            text-transform: uppercase;
            color: #fff;
            background: linear-gradient(45deg, #ff00ff, #d100d1);
            border: none;
            border-radius: 8px;
            box-shadow: 0 0 10px #ff00ff, 0 0 20px #ff00ff;
            text-decoration: none;
            transition: 0.3s;
        }
        .teacher-link:hover {
            background: linear-gradient(45deg, #d100d1, #ff00ff);
            box-shadow: 0 0 20px #ff00ff, 0 0 40px #ff00ff;
            transform: scale(1.05);
        }

        /* Form that holds the theme buttons */
        form {
            margin-top: 10px;
        }
        /* Theme buttons: wide, with spacing and neon effect */
        .theme-button {
            display: block;
            width: 90%;
            max-width: 600px;
            margin: 15px auto;
            padding: 14px 0;
            background: linear-gradient(45deg, #ff00ff, #d100d1);
            color: #fff;
            font-size: 1.2rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            box-shadow: 0 0 10px #ff00ff, 0 0 20px #ff00ff;
            transition: 0.3s ease;
        }
        .theme-button:hover {
            background: linear-gradient(45deg, #d100d1, #ff00ff);
            box-shadow: 0 0 20px #ff00ff, 0 0 40px #ff00ff;
            transform: scale(1.03);
        }
    </style>
</head>
<body>
    <div class="odabir-teme-container">
        <h2>Odaberite temu</h2>

        <!-- If teacher, show a vibrant "Dodaj novo pitanje" button -->
        <?php if ($userLevel == 1): ?>
            <a href="dodaj_pitanje.php" class="teacher-link">+ DODAJ NOVO PITANJE</a>
        <?php endif; ?>

        <form action="index.php" method="POST">
            <?php foreach ($korisnikTeme as $tema): ?>
                <button 
                    class="theme-button" 
                    type="submit" 
                    name="tema_id" 
                    value="<?= htmlspecialchars($tema['theme_id']) ?>">
                    <?= htmlspecialchars($tema['theme_name']) ?>
                    (<?= htmlspecialchars($tema['broj_pitanja']) ?>)
                </button>
            <?php endforeach; ?>
        </form>
    </div>
</body>
</html>
