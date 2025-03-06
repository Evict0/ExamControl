<?php
session_start();

// Check if user is logged in and topics are selected
if (!isset($_SESSION['user_id']) || empty($_POST['teme'])) {
    header('Location: odabir_teme.php');
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

$userId = $_SESSION['user_id'];
$teme = $_POST['teme'];

// Save selected topics
$stmt = $conn->prepare("INSERT IGNORE INTO ep_korisnik_teme (korisnik_id, tema_id) VALUES (?, ?)");
foreach ($teme as $temaId) {
    $stmt->execute([$userId, $temaId]);
}

// After saving, redirect as needed (here back to login page)
header('Location: login.php');
exit();
?>
