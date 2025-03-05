<?php
session_start();

// Ako korisnik nije prijavljen, preusmjeri ga na login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Povezivanje na bazu
$servername = "localhost";
$dbUsername = "root";
$dbPassword = "";
$dbname     = "kviz2";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $dbUsername, $dbPassword);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Dohvati sve teme i broj pitanja
    $sql = "SELECT t.ID, t.naziv, COUNT(p.ID) AS broj_pitanja
            FROM ep_teme t
            LEFT JOIN ep_pitanje p ON t.ID = p.temaID
            GROUP BY t.ID
            ORDER BY t.naziv";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $teme = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Obrada POST-a: sprema u sesiju 'temaID' i 'temaNaziv' pa šalje na index.php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['temaID'], $_POST['temaNaziv'])) {
            $_SESSION['temaID'] = $_POST['temaID'];
            $_SESSION['temaNaziv'] = $_POST['temaNaziv'];

            header("Location: index.php");
            exit();
        }
    }
} catch (PDOException $e) {
    echo "Greška pri povezivanju s bazom: " . $e->getMessage();
    exit();
}
?>
<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <title>Odaberi temu</title>
    <link rel="stylesheet" href="style.css">
    <!-- Stil klasa tema-btn (ako već nije u style.css, ostavite ovdje) -->
    <style>
        .theme-buttons {
            text-align: center;
            margin-top: 20px;
        }
        .tema-btn {
            background-color: #ff00ff;
            color: #fff;
            padding: 14px 28px;
            font-size: 1.1rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin: 10px 5px 0 5px;
            box-shadow: 0 0 5px #ff00ff, 0 0 10px #ff00ff;
            transition: 0.3s ease;
        }
        .tema-btn:hover {
            background-color: #d100d1;
            box-shadow: 0 0 10px #ff00ff, 0 0 20px #ff00ff;
        }
    </style>
</head>
<body>
    <div class="quiz-container">
        <h2>Odaberite temu kviza:</h2>

        <!-- Forma: svaki <button> šalje 'temaID' i odgovarajući 'temaNaziv' -->
        <form method="POST" class="theme-buttons">
            <?php foreach ($teme as $tema): ?>
                <?php if ($tema['broj_pitanja'] > 0): ?>
                    <button type="submit" name="temaID" 
                            value="<?= htmlspecialchars($tema['ID']) ?>" 
                            class="tema-btn">
                        <?= htmlspecialchars($tema['naziv']) ?> 
                        (<?= (int)$tema['broj_pitanja'] ?> pitanja)
                    </button>
                    <!-- Hidden input nosi aktualni naziv te teme -->
                    <input type="hidden" 
                           name="temaNaziv" 
                           value="<?= htmlspecialchars($tema['naziv']) ?>">
                <?php endif; ?>
            <?php endforeach; ?>
        </form>

        <!-- Ako je korisnik profesor (razina=1), vidi gumb 'Dodaj pitanje' -->
        <?php if (isset($_SESSION['razina']) && $_SESSION['razina'] == 1): ?>
            <div class="theme-buttons">
                <a href="dodaj.php" style="text-decoration: none;">
                    <button type="button" class="tema-btn">Dodaj pitanje</button>
                </a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
