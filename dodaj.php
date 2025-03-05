<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

// Database connection details
$servername = "localhost";
$dbUsername = "root";
$dbPassword = "";
$dbname     = "kviz2";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $dbUsername, $dbPassword);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 1) Retrieve themes (for the <select> element)
    $stmt = $conn->prepare("SELECT ID, naziv FROM ep_teme ORDER BY naziv");
    $stmt->execute();
    $popisTema = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $poruka = ""; // Message to display status
    $hint = ""; // Holds the hint value

    // 2) Process the form when submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Retrieve form fields
        $question    = $_POST['question'] ?? '';
        $answer1     = $_POST['answer1'] ?? '';
        $answer2     = $_POST['answer2'] ?? '';
        $answer3     = $_POST['answer3'] ?? '';
        $answer4     = $_POST['answer4'] ?? '';
        $correct     = $_POST['correctAnswer'] ?? '';
        $selectedID  = $_POST['temaID'] ?? '';   // Selected theme from dropdown
        $newTheme    = trim($_POST['newTheme'] ?? ''); // New theme text
        $hint        = $_POST['hint'] ?? '';    // Hint text

        // Simple validation
        if (
            empty($question) || 
            empty($answer1)  || 
            empty($answer2)  || 
            empty($answer3)  || 
            empty($answer4)  || 
            empty($correct)
        ) {
            $poruka = "Molim popunite sva obavezna polja (pitanje, 4 odgovora i točan odgovor).";
        } else {
            // If a new theme is provided, insert it and get its ID
            if (!empty($newTheme)) {
                $sqlNovaTema = "INSERT INTO ep_teme (naziv) VALUES (:naziv)";
                $stmtTema = $conn->prepare($sqlNovaTema);
                $stmtTema->bindValue(':naziv', $newTheme);
                $stmtTema->execute();
                $temaID = $conn->lastInsertId();
            } else {
                // If no new theme is provided, use the dropdown selection
                if (empty($selectedID)) {
                    $poruka = "Molim odaberite postojeću temu ili unesite novu.";
                    goto skipInsert;
                }
                $temaID = $selectedID;
            }

            // Process file upload for question image, if provided
            $imagePath = null;
            if (isset($_FILES['questionImage']) && $_FILES['questionImage']['error'] === UPLOAD_ERR_OK) {
                $maxFileSize = 2 * 1024 * 1024; // 2 MB
                if ($_FILES['questionImage']['size'] <= $maxFileSize) {
                    $allowedTypes = ["image/jpeg", "image/png", "image/gif"];
                    if (in_array($_FILES['questionImage']['type'], $allowedTypes)) {
                        $uploadDir = "uploads/";
                        if (!is_dir($uploadDir)) {
                            mkdir($uploadDir, 0777, true);
                        }
                        $fileTmpPath = $_FILES['questionImage']['tmp_name'];
                        $fileName = basename($_FILES['questionImage']['name']);
                        $fileName = preg_replace('/[^A-Za-z0-9.\-_]/', '', $fileName);
                        $destPath = $uploadDir . time() . "_" . $fileName;
                        if (move_uploaded_file($fileTmpPath, $destPath)) {
                            $imagePath = $destPath;
                        } else {
                            $poruka = "Greška pri uploadanju slike.";
                            goto skipInsert;
                        }
                    } else {
                        $poruka = "Format slike nije podržan. Koristite JPG, PNG ili GIF.";
                        goto skipInsert;
                    }
                } else {
                    $poruka = "Veličina slike prelazi maksimalnih 2MB.";
                    goto skipInsert;
                }
            }

            // 3) Insert the question into the ep_pitanje table
            $sqlPitanje = "INSERT INTO ep_pitanje (tekst_pitanja, korisnikID, brojBodova, temaID, slika, hint)
                           VALUES (:tekst, :korisnikID, :bodovi, :temaID, :slika, :hint)";
            $stmtPitanje = $conn->prepare($sqlPitanje);
            $stmtPitanje->bindValue(':tekst', $question);
            $stmtPitanje->bindValue(':korisnikID', $_SESSION['user_id']);
            $stmtPitanje->bindValue(':bodovi', 1);
            $stmtPitanje->bindValue(':temaID', $temaID);
            $stmtPitanje->bindValue(':slika', $imagePath);
            $stmtPitanje->bindValue(':hint', $hint);
            $stmtPitanje->execute();

            $newQuestionID = $conn->lastInsertId();

            // 4) Insert the 4 answers into the op_odgovori table
            $answers = [$answer1, $answer2, $answer3, $answer4];
            $correctIndex = (int)$correct - 1;
            for ($i = 0; $i < 4; $i++) {
                $sqlOdgovori = "INSERT INTO op_odgovori (tekst, pitanjeID, tocno, korisnikID, aktivno)
                                VALUES (:tekst, :pitanjeID, :tocno, :korisnikID, 1)";
                $stmtOdgovor = $conn->prepare($sqlOdgovori);
                $stmtOdgovor->bindValue(':tekst', $answers[$i]);
                $stmtOdgovor->bindValue(':pitanjeID', $newQuestionID);
                $stmtOdgovor->bindValue(':tocno', ($i === $correctIndex) ? 1 : 0);
                $stmtOdgovor->bindValue(':korisnikID', $_SESSION['user_id']);
                $stmtOdgovor->execute();
            }

            $poruka = "Pitanje uspješno dodano u bazu!";
        }
        skipInsert:;
    }
} catch (PDOException $e) {
    die("Greška s bazom: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <title>Dodaj pitanje</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Additional styles for the form */
        .dodaj-form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        .dodaj-form .form-group {
            display: flex;
            flex-direction: column;
        }
        .dodaj-form label {
            margin-bottom: 5px;
            font-weight: 600;
            color: #ffae00;
            text-shadow: 0 0 5px #ffae00;
        }

        /* Common style for both textareas */
        #question,
        #hint {
            background-color: #2a2a2a;
            border: 2px solid #ff00ff;
            border-radius: 5px;
            padding: 10px;
            color: #fff;
            box-shadow: inset 0 0 5px rgba(255, 0, 255, 0.3);
            font-size: 1rem;
            width: 100%;
            box-sizing: border-box;
            display: block;
        }

        /* Specific height settings */
        #question {
            height: 80px;  /* Height for the question box */
        }
        #hint {
            height: calc(105px / 3); 
        }

        /* Styles for text inputs and select */
        .dodaj-form input[type="text"],
        .dodaj-form select {
            background-color: #1c1c1c;
            border: 2px solid #ff00ff;
            border-radius: 5px;
            padding: 10px;
            color: #fff;
            box-shadow: inset 0 0 5px rgba(255, 0, 255, 0.3);
            font-size: 1rem;
        }

        /* Radio buttons styling */
        .radio-group {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        .radio-group label {
            margin: 0;
        }

        /* Submit button styling */
        .dodaj-form button[type="submit"] {
            align-self: flex-start;
            background-color: #ff00ff;
            color: #fff;
            padding: 14px 36px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1.1rem;
            margin-top: 10px;
            box-shadow: 0 0 5px #ff00ff, 0 0 10px #ff00ff;
            transition: 0.3s ease;
        }
        .dodaj-form button[type="submit"]:hover {
            background-color: #d100d1;
            box-shadow: 0 0 10px #ff00ff, 0 0 20px #ff00ff;
        }

        /* Message style */
        .dodaj-poruka {
            color: #40ffe5;
            margin-bottom: 10px;
            text-shadow: 0 0 5px #40ffe5;
        }
    </style>
</head>
<body>
    <div class="quiz-container">
        <h2>Dodaj novo pitanje</h2>
        <?php if (!empty($poruka)) : ?>
            <p class="dodaj-poruka"><?= htmlspecialchars($poruka) ?></p>
        <?php endif; ?>
        <form method="POST" class="dodaj-form" enctype="multipart/form-data">
            <!-- Question text -->
            <div class="form-group">
                <label for="question">Tekst pitanja:</label>
                <textarea id="question" name="question" rows="3" cols="50"></textarea>
            </div>
            <!-- Answers 1-4 -->
            <div class="form-group">
                <label for="answer1">Odgovor 1:</label>
                <input type="text" id="answer1" name="answer1">
            </div>
            <div class="form-group">
                <label for="answer2">Odgovor 2:</label>
                <input type="text" id="answer2" name="answer2">
            </div>
            <div class="form-group">
                <label for="answer3">Odgovor 3:</label>
                <input type="text" id="answer3" name="answer3">
            </div>
            <div class="form-group">
                <label for="answer4">Odgovor 4:</label>
                <input type="text" id="answer4" name="answer4">
            </div>
            <!-- Hint textarea -->
            <div class="form-group">
                <label for="hint">Hint (savjet):</label>
                <textarea id="hint" name="hint" rows="3" cols="50"><?= htmlspecialchars($hint) ?></textarea>
            </div>
            <!-- Correct answer radio buttons -->
            <div class="form-group">
                <label>Koji je točan odgovor?</label>
                <div class="radio-group">
                    <input type="radio" id="correct1" name="correctAnswer" value="1">
                    <label for="correct1">1</label>
                    <input type="radio" id="correct2" name="correctAnswer" value="2">
                    <label for="correct2">2</label>
                    <input type="radio" id="correct3" name="correctAnswer" value="3">
                    <label for="correct3">3</label>
                    <input type="radio" id="correct4" name="correctAnswer" value="4">
                    <label for="correct4">4</label>
                </div>
            </div>
            <!-- Theme selection -->
            <div class="form-group">
                <label for="temaID">Odaberite postojeću temu:</label>
                <select name="temaID" id="temaID">
                    <option value="">-- Odaberite temu --</option>
                    <?php foreach($popisTema as $t) : ?>
                        <option value="<?= htmlspecialchars($t['ID']) ?>">
                            <?= htmlspecialchars($t['naziv']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="newTheme">Ili upišite novu temu (ostavite prazno ako birate postojeću):</label>
                <input type="text" id="newTheme" name="newTheme">
            </div>
            <!-- Image upload -->
            <div class="form-group">
                <label for="questionImage">Dodajte sliku za pitanje (maks. 2MB, format: JPG, PNG, GIF):</label>
                <div class="file-upload">
                    <label for="questionImage" class="upload-btn">📸 Odaberi sliku</label>
                    <input type="file" id="questionImage" name="questionImage" accept="image/*" hidden>
                </div>
                <p id="imageName" class="image-name"></p>
                <p id="imageError" class="error-message" style="display: none;">❌ Slika mora biti manja od 2 MB!</p>
                <div id="imagePreviewContainer" class="image-preview-container" style="display: none;">
                    <img id="imagePreview" src="" alt="Pregled slike">
                </div>
            </div>
            <button type="submit">Spremi pitanje</button>
        </form>
        <br><br>
        <!-- Button to go back to theme selection -->
        <a href="odabir_teme.php" style="text-decoration: none;">
            <button class="tema-btn" type="button">Natrag na odabir tema</button>
        </a>
    </div>
    <script>
        document.getElementById('questionImage').addEventListener('change', function(event) {
            const file = event.target.files[0];
            const maxSize = 2 * 1024 * 1024; // 2 MB
            const imageNameDisplay = document.getElementById('imageName');
            const imageErrorDisplay = document.getElementById('imageError');
            const imagePreviewContainer = document.getElementById('imagePreviewContainer');
            const imagePreview = document.getElementById('imagePreview');

            if (file) {
                if (file.size > maxSize) {
                    imageErrorDisplay.style.display = 'block';
                    event.target.value = ""; // Reset input
                    imagePreviewContainer.style.display = 'none';
                    imageNameDisplay.innerHTML = "";
                    return;
                } else {
                    imageErrorDisplay.style.display = 'none';
                }
                // Display the image name
                imageNameDisplay.innerHTML = `📂 Odabrana slika: <strong>${file.name}</strong>`;
                // Display the image preview
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                    imagePreviewContainer.style.display = 'flex';
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html>
