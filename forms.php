<?php
session_start();

/**
 * Load quiz questions from index.php.
 * Optionally include the tema parameter if provided either via POST or via SESSION.
 */
function loadQuizQuestions() {
    $host = $_SERVER['HTTP_HOST'];
    $uri  = rtrim(dirname($_SERVER['REQUEST_URI']), '/\\');
    
    // Build query string with getQuestions=1
    $query = "getQuestions=1";
    
    // Provjera – prvo POST, pa sesija (temaID)
    if (isset($_POST['tema']) && trim($_POST['tema']) !== '') {
        $tema = urlencode($_POST['tema']);
        $query .= "&tema=" . $tema;
    } elseif (isset($_SESSION['temaID']) && trim($_SESSION['temaID']) !== '') {
        $tema = urlencode($_SESSION['temaID']);
        $query .= "&tema=" . $tema;
    }
    
    $url  = "http://$host$uri/index.php?" . $query;
    $json = file_get_contents($url);
    if ($json === false) {
        die("Error fetching questions from index.php");
    }
    $questions = json_decode($json, true);
    if ($questions === null) {
        die("Error decoding questions JSON");
    }
    return $questions;
}

// Load the questions used in the quiz
$questions = loadQuizQuestions();

// Prepare arrays to store correct and incorrect answers
$correctAnswers = [];
$wrongAnswers   = [];
$score = 0;

foreach ($questions as $index => $question) {
    $qKey = "question" . $index;
    $userAnswer = isset($_POST[$qKey]) ? $_POST[$qKey] : "";
    $answerOptions = explode("|", $question["answers"]);
    $correctIndex = $question["correctAnswer"];
    
    $correctAnswerText = isset($answerOptions[$correctIndex]) ? $answerOptions[$correctIndex] : "Nije definirano";
    $userAnswerText = isset($answerOptions[$userAnswer]) ? $answerOptions[$userAnswer] : "Nije odabrano";
    
    // Koristimo hint kao objašnjenje
    $explanation = $question["hint"] ?: "Nema dodatnog objašnjenja.";
    
    if ($userAnswer === $correctIndex) {
        $score++;
        $correctAnswers[] = [
            "question"       => $question["question"],
            "your_answer"    => $userAnswerText,
            "correct_answer" => $correctAnswerText,
            "explanation"    => $explanation
        ];
    } else {
        $wrongAnswers[] = [
            "question"       => $question["question"],
            "your_answer"    => $userAnswerText,
            "correct_answer" => $correctAnswerText,
            "explanation"    => $explanation
        ];
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rezultati Kviza</title>
    <style>
        /* Globalni stilovi */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        body {
            background: #090909;
            background-image: linear-gradient(145deg, #1b1b1b, #000);
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        .results-container {
            width: 100%;
            max-width: 1200px;
            background: linear-gradient(145deg, #1b1b1b, #000);
            border: 2px solid #ff00ff;
            box-shadow: 0 0 20px rgba(255, 0, 255, 0.2), 0 0 60px rgba(255, 0, 255, 0.1);
            border-radius: 12px;
            padding: 30px;
        }
        h1, h2 {
            text-align: center;
            color: #ffae00;
            text-shadow: 0 0 8px #ffae00;
            margin-bottom: 20px;
        }
        .score {
            text-align: center;
            font-size: 1.6rem;
            margin-bottom: 30px;
            color: #40ffe5;
            text-shadow: 0 0 5px #40ffe5;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ff00ff;
            color: #fff;
        }
        th {
            background-color: rgba(255, 0, 255, 0.2);
            text-shadow: 0 0 5px #ff00ff;
        }
        .correct {
            background-color: rgba(200, 230, 201, 0.2);
            box-shadow: inset 0 0 10px #40ffe5;
        }
        .incorrect {
            background-color: rgba(255, 205, 210, 0.2);
            box-shadow: inset 0 0 10px #ff00ff;
        }
        .results-btn {
            display: block;
            width: 250px;
            margin: 0 auto 20px auto;
            padding: 14px 28px;
            background-color: #ff00ff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1.1rem;
            text-align: center;
            box-shadow: 0 0 10px #ff00ff, 0 0 20px #ff00ff;
            transition: 0.3s ease;
        }
        .results-btn:hover {
            background-color: #d100d1;
            box-shadow: 0 0 15px #ff00ff, 0 0 30px #ff00ff;
        }
    </style>
</head>
<body>
<div class="results-container">
    <h1>Rezultati Kviza</h1>
    <p class="score"><strong>Ukupno bodova:</strong> <?php echo $score; ?> od <?php echo count($questions); ?></p>
    
    <h2>Točni Odgovori</h2>
    <?php if (!empty($correctAnswers)) { ?>
        <table>
            <tr>
                <th>Pitanje</th>
                <th>Vaš Odgovor</th>
                <th>Točan Odgovor</th>
                <th>Objašnjenje</th>
            </tr>
            <?php foreach ($correctAnswers as $item) { ?>
            <tr class="correct">
                <td><?php echo htmlspecialchars($item["question"]); ?></td>
                <td><?php echo htmlspecialchars($item["your_answer"]); ?></td>
                <td><?php echo htmlspecialchars($item["correct_answer"]); ?></td>
                <td><?php echo htmlspecialchars($item["explanation"]); ?></td>
            </tr>
            <?php } ?>
        </table>
    <?php } else { ?>
        <p style="text-align:center;">Niste imali točnih odgovora.</p>
    <?php } ?>
    
    <h2>Netočni Odgovori</h2>
    <?php if (!empty($wrongAnswers)) { ?>
        <table>
            <tr>
                <th>Pitanje</th>
                <th>Vaš Odgovor</th>
                <th>Točan Odgovor</th>
                <th>Objašnjenje</th>
            </tr>
            <?php foreach ($wrongAnswers as $item) { ?>
            <tr class="incorrect">
                <td><?php echo htmlspecialchars($item["question"]); ?></td>
                <td><?php echo htmlspecialchars($item["your_answer"]); ?></td>
                <td><?php echo htmlspecialchars($item["correct_answer"]); ?></td>
                <td><?php echo htmlspecialchars($item["explanation"]); ?></td>
            </tr>
            <?php } ?>
        </table>
    <?php } else { ?>
        <p style="text-align:center;">Svi odgovori su točni!</p>
    <?php } ?>
    
<!-- Optional: Button to try the quiz again -->
<form action="odabir_Teme.php" method="post">
    <input type="submit" name="retry" value="Pokušaj ponovo" class="results-btn">
</form>

</div>
</body>
</html>
