<?php
// db_connection.php
$servername = "localhost";
$dbUsername = "examportalsql";
$dbPassword = '1dBL$oV+e?RD';
$dbname     = "zavrsni2024";

// Kreiranje konekcije pomoću MySQLi
$conn = new mysqli($servername, $dbUsername, $dbPassword, $dbname);

// Provjera konekcije
if ($conn->connect_error) {
    die("Konekcija nije uspjela: " . $conn->connect_error);
}
?>
