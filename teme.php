<?php
session_start();
include 'db_connection.php'; // or your PDO connection code

$stmt = $conn->query("SELECT ID, naziv FROM ep_teme");
$teme = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <title>Odabir tema</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2>Odaberite željene teme</h2>
        <form method="POST" action="spremi_teme.php">
            <?php foreach ($teme as $tema): ?>
                <div class="form-group">
                    <label>
                        <input type="checkbox" name="teme[]" value="<?= htmlspecialchars($tema['ID']) ?>">
                        <?= htmlspecialchars($tema['naziv']) ?>
                    </label>
                </div>
            <?php endforeach; ?>
            <button type="submit">Spremi teme</button>
        </form>
    </div>
</body>
</html>
