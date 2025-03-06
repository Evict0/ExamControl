<?php
session_start();

/**
 * If ?getQuestions=1 is in the URL, we return JSON immediately.
 * Otherwise, we load the normal HTML quiz page below.
 */
if (isset($_GET['getQuestions']) && $_GET['getQuestions'] == 1) {
    // Database connection settings
    $host    = 'localhost';
    $db      = 'kviz2';
    $user    = 'root';
    $pass    = '';
    $charset = 'utf8mb4';

    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $options = [
         PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
         PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
         PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    try {
         $pdo = new PDO($dsn, $user, $pass, $options);
    } catch (PDOException $e) {
         http_response_code(500);
         echo json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]);
         exit;
    }

    // Check if ?tema=... or if session has 'temaID'
    $tema = '';
    if (isset($_GET['tema']) && trim($_GET['tema']) !== '') {
         $tema = trim($_GET['tema']);
    } elseif (isset($_SESSION['temaID']) && trim($_SESSION['temaID']) !== '') {
         $tema = trim($_SESSION['temaID']);
    }

    // Build the query
    if ($tema !== '') {
        $stmt = $pdo->prepare("
            SELECT ID, tekst_pitanja, hint, slika 
            FROM ep_pitanje 
            WHERE aktivno = 1 AND temaID = :tema
            ORDER BY ID
        ");
        $stmt->execute([':tema' => $tema]);
    } else {
        // If no theme is given, we can return all active questions or an empty set.
        $stmt = $pdo->query("
            SELECT ID, tekst_pitanja, hint, slika 
            FROM ep_pitanje 
            WHERE aktivno = 1
            ORDER BY ID
        ");
    }
    $questionsData = $stmt->fetchAll();

    // Process questions and answers
    $questions = [];
    foreach ($questionsData as $q) {
        $questionId = $q['ID'];

        // Fetch answers
        $stmtAnswers = $pdo->prepare("
            SELECT tekst, tocno 
            FROM op_odgovori 
            WHERE pitanjeID = :qid AND aktivno = 1
            ORDER BY ID
        ");
        $stmtAnswers->execute([':qid' => $questionId]);
        $answersRows = $stmtAnswers->fetchAll();

        $answers = [];
        $correctAnswerIndex = null;
        foreach ($answersRows as $index => $row) {
            $answers[] = $row['tekst'];
            if ($row['tocno'] == 1) {
                $correctAnswerIndex = $index;
            }
        }
        // Join answers with "|"
        $answersPipe = implode('|', $answers);

        if ($correctAnswerIndex === null) {
            // If no correct answer is found, set it to -1
            $correctAnswerIndex = -1;
        }

        $questions[] = [
            'question'      => $q['tekst_pitanja'],
            'answers'       => $answersPipe,
            'correctAnswer' => (string)$correctAnswerIndex,
            'hint'          => $q['hint'] ?? '',
            'image'         => $q['slika'] ?? ''
        ];
    }

    // Output as JSON
    header('Content-Type: application/json');
    echo json_encode($questions);
    exit;
}

// ============== NORMAL (HTML) QUIZ PAGE ==============

// If a user posts 'tema_id', store it in session
if (isset($_POST['tema_id'])) {
    $_SESSION['temaID'] = $_POST['tema_id'];
}

// If no theme is in session, redirect to topic selection
if (!isset($_SESSION['temaID'])) {
    header("Location: odabir_teme.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="hr">
<head>
  <meta charset="UTF-8">
  <title>Mafija Kviz</title>
  <style>
    /* Basic reset and neon styling */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Poppins', sans-serif;
    }
    body {
        background: #090909;
        color: #fff;
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
    }
    .quiz-container {
        width: 90%;
        max-width: 1200px;
        margin: 20px;
        padding: 30px;
        background: linear-gradient(145deg, #1b1b1b, #000);
        border: 2px solid #ff00ff;
        box-shadow: 0 0 20px rgba(255,0,255,0.2), 0 0 60px rgba(255,0,255,0.1);
        border-radius: 12px;
        display: flex;
        flex-direction: column;
        min-height: 80vh;
    }
    /* Question number styling */
    #question-number {
        background: rgba(64, 255, 229, 0.2);
        border: 2px solid #40ffe5;
        border-radius: 8px;
        padding: 10px;
        margin-bottom: 15px;
        text-align: center;
        font-size: 1.8rem;
        font-weight: bold;
        color: #40ffe5;
        text-shadow: 0 0 5px #40ffe5;
    }
    /* Container for question and hint */
    .question-hint-container {
        display: flex;
        width: 100%;
        margin-bottom: 20px;
        align-items: flex-start;
    }
    .question-box {
        background-color: #1c1c1c;
        padding: 30px;
        border-radius: 8px;
        margin-right: 20px;
        flex: 3;
        border: 2px solid #ff00ff;
        box-shadow: inset 0 0 15px rgba(255, 0, 255, 0.1);
    }
    .question-box h2 {
        font-size: 2rem;
        color: #ffae00;
        text-shadow: 0 0 8px #ffae00;
    }
    .hint-box {
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        align-items: flex-start;
    }
    /* Hint button styling */
    #hint-btn {
        background: #ff00ff;
        color: #fff;
        padding: 14px 28px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 1.1rem;
        margin-bottom: 10px;
        transition: 0.3s ease;
        box-shadow: 0 0 5px #ff00ff, 0 0 10px #ff00ff;
    }
    #hint-btn:hover {
        background: #d100d1;
        box-shadow: 0 0 10px #ff00ff, 0 0 20px #ff00ff;
    }
    #hint {
        font-style: italic;
        color: #fff;
        display: none;
        font-size: 1.1rem;
        margin-top: 5px;
        background-color: rgba(255, 0, 255, 0.15);
        padding: 10px;
        border-radius: 5px;
        box-shadow: inset 0 0 10px rgba(255, 0, 255, 0.1);
    }
    /* Answers layout */
    .answers-box {
        width: 100%;
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 0;
        min-height: 250px;
        margin-top: 20px;
    }
    .answer-btn {
        background-color: #000;
        color: #fff;
        border: 2px solid #ff00ff;
        display: flex;
        justify-content: center;
        align-items: center;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 1.2rem;
        position: relative;
        box-shadow: inset 0 0 8px #ff00ff;
    }
    .answer-btn:hover {
        background-color: #111;
        color: #ffae00;
        box-shadow: inset 0 0 15px rgba(255,175,0,0.5);
    }
    .answer-btn.selected {
        background-color: #222;
        box-shadow: 0 0 15px #ffae00, 0 0 25px #ffae00;
    }
    /* Next button */
    #next-button {
        background-color: #ff00ff;
        color: #fff;
        padding: 14px 28px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 1.1rem;
        margin-top: 20px;
        align-self: center;
        box-shadow: 0 0 5px #ff00ff, 0 0 10px #ff00ff;
        transition: 0.3s ease;
    }
    #next-button:hover {
        background-color: #d100d1;
        box-shadow: 0 0 10px #ff00ff, 0 0 20px #ff00ff;
        transform: scale(1.02);
    }
    #next-button:disabled {
        background-color: #555;
        cursor: not-allowed;
        box-shadow: none;
    }
    /* Question image */
    #question-image {
        max-width: 100%;
        max-height: 300px;
        width: auto;
        height: auto;
        object-fit: contain;
        cursor: pointer;
    }
    /* Modal for enlarged image */
    .modal {
        display: none;
        position: fixed;
        z-index: 9999;
        padding-top: 60px;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0,0,0,0.9);
    }
    .modal-content {
        margin: auto;
        display: block;
        max-width: 90%;
        max-height: 90%;
    }
    #caption {
        margin: auto;
        display: block;
        width: 80%;
        max-width: 700px;
        text-align: center;
        color: #ccc;
        padding: 10px 0;
    }
    .close {
        position: absolute;
        top: 30px;
        right: 35px;
        color: #f1f1f1;
        font-size: 40px;
        font-weight: bold;
        cursor: pointer;
    }
    .close:hover,
    .close:focus {
        color: #bbb;
        text-decoration: none;
    }
    @media (max-width: 768px) {
        .quiz-container {
            width: 95%;
            padding: 20px;
        }
        .question-hint-container {
            flex-direction: column;
        }
        .question-box {
            margin-right: 0;
            margin-bottom: 20px;
        }
        .answers-box {
            grid-template-columns: 1fr;
        }
        .answer-btn {
            width: 100%;
        }
    }
  </style>
</head>
<body>
  <div class="quiz-container">
    <div id="question-number"></div>
    <div class="question-hint-container">
      <div class="question-box">
        <h2 id="question"></h2>
        <div id="question-image-container" style="display:none;">
          <img id="question-image" src="" alt="Slika pitanja">
        </div>
      </div>
      <div class="hint-box">
        <button id="hint-btn">Prikaži savjet</button>
        <div id="hint"></div>
      </div>
    </div>
    <div class="answers-box">
      <button class="answer-btn"></button>
      <button class="answer-btn"></button>
      <button class="answer-btn"></button>
      <button class="answer-btn"></button>
    </div>
    <button id="next-button" disabled>Sljedeće pitanje</button>
  </div>

  <!-- Modal for enlarged image -->
  <div id="imageModal" class="modal">
    <span class="close">&times;</span>
    <img class="modal-content" id="modalImage">
    <div id="caption"></div>
  </div>

  <script>
    // DOM references
    const questionElement       = document.getElementById("question");
    const answerButtons         = document.querySelectorAll(".answer-btn");
    const hintButton            = document.getElementById("hint-btn");
    const hintElement           = document.getElementById("hint");
    const nextButton            = document.getElementById("next-button");
    const questionNumberElement = document.getElementById("question-number");
    const questionImageContainer= document.getElementById("question-image-container");
    const questionImage         = document.getElementById("question-image");

    // Modal for enlarged image
    const modal      = document.getElementById("imageModal");
    const modalImage = document.getElementById("modalImage");
    const captionText= document.getElementById("caption");
    const closeSpan  = document.getElementsByClassName("close")[0];

    let questions = [];
    let currentQuestionIndex = 0;
    let userAnswers = [];

    // Fetch questions from the server
    async function loadQuestionsFromDB() {
      try {
        // Add the existing query string to ?getQuestions=1
        // Or you can just do: fetch("index.php?getQuestions=1")
        const response = await fetch("index.php?getQuestions=1" + window.location.search);
        if (!response.ok) {
          console.error("Server error:", response.status, response.statusText);
          questionElement.innerText = "Greška pri učitavanju pitanja!";
          return;
        }
        questions = await response.json();
        loadQuestion();
      } catch (error) {
        questionElement.innerText = "Greška na serveru!";
        console.error("Error fetching questions:", error);
      }
    }

    // Load a single question
    function loadQuestion() {
      resetState();
      const q = questions[currentQuestionIndex];
      questionElement.innerText = q.question;
      
      if (q.image && q.image.trim() !== "") {
        questionImage.src = q.image;
        questionImageContainer.style.display = "block";
      } else {
        questionImageContainer.style.display = "none";
      }

      const possibleAnswers = q.answers.split("|");
      answerButtons.forEach((btn, idx) => {
        if (possibleAnswers[idx]) {
          btn.style.display = "flex";
          btn.innerText = possibleAnswers[idx];
          btn.dataset.index = idx;
          btn.classList.remove("selected");
        } else {
          btn.style.display = "none";
        }
      });

      questionNumberElement.innerText = `Trenutno pitanje: ${currentQuestionIndex + 1} od ${questions.length}`;
      hintElement.innerText = q.hint || "Nema savjeta za ovo pitanje.";
      hintElement.style.display = "none";
      hintButton.innerText = "Prikaži savjet";
      nextButton.disabled = true;
    }

    function resetState() {
      answerButtons.forEach(btn => {
        btn.disabled = false;
        btn.classList.remove("selected");
      });
    }

    function selectAnswer(e) {
      answerButtons.forEach(btn => btn.classList.remove("selected"));
      e.target.classList.add("selected");
      userAnswers[currentQuestionIndex] = e.target.dataset.index;
      nextButton.disabled = false;
    }

    function nextQuestion() {
      currentQuestionIndex++;
      if (currentQuestionIndex < questions.length) {
        loadQuestion();
      } else {
        finishQuiz();
      }
    }

    // Submit answers to forms.php
    function finishQuiz() {
      const form = document.createElement("form");
      form.method = "post";
      form.action = "forms.php";
      userAnswers.forEach((answer, idx) => {
        const input = document.createElement("input");
        input.type = "hidden";
        input.name = "question" + idx;
        input.value = answer;
        form.appendChild(input);
      });
      // If the URL has &tema=..., pass that along
      const params = new URLSearchParams(window.location.search);
      if (params.has("tema")) {
        const temaInput = document.createElement("input");
        temaInput.type = "hidden";
        temaInput.name = "tema";
        temaInput.value = params.get("tema");
        form.appendChild(temaInput);
      }
      document.body.appendChild(form);
      form.submit();
    }

    function toggleHint() {
      if (hintElement.style.display === "none") {
        hintElement.style.display = "block";
        hintButton.innerText = "Sakrij savjet";
      } else {
        hintElement.style.display = "none";
        hintButton.innerText = "Prikaži savjet";
      }
    }

    // Modal for question image
    questionImage.addEventListener("click", function() {
      modal.style.display = "block";
      modalImage.src = this.src;
      captionText.innerText = this.alt;
    });
    closeSpan.addEventListener("click", function() {
      modal.style.display = "none";
    });
    modal.addEventListener("click", function(event) {
      if (event.target === modal) {
        modal.style.display = "none";
      }
    });

    answerButtons.forEach(btn => btn.addEventListener("click", selectAnswer));
    nextButton.addEventListener("click", nextQuestion);
    hintButton.addEventListener("click", toggleHint);

    // Load questions on page load
    loadQuestionsFromDB();
  </script>
</body>
</html>
