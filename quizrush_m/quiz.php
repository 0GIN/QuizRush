
<?php
session_start();
require_once '../config/db.php';
include '../partials/header.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<div class='container'><p>Niepoprawny identyfikator quizu.</p></div>";
    include '../partials/footer.php';
    exit;
}

$quiz_id = (int) $_GET['id'];

// Pobierz quiz
$stmtQuiz = $pdo->prepare("SELECT title FROM quizzes WHERE id = ?");
$stmtQuiz->execute([$quiz_id]);
$quiz = $stmtQuiz->fetch();

if (!$quiz) {
    echo "<div class='container'><p>Ten quiz nie istnieje.</p></div>";
    include '../partials/footer.php';
    exit;
}

// Jeżeli formularz został wysłany – sprawdź odpowiedzi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $score = 0;
    $total = 0;
    // Pobierz wszystkie pytania i poprawne odpowiedzi
    $questionStmt = $pdo->prepare("
        SELECT q.id AS qid, a.id AS aid, a.is_correct
        FROM questions q
        JOIN answers a ON a.question_id = q.id
        WHERE q.quiz_id = ?
    ");
    $questionStmt->execute([$quiz_id]);
    $answers = $questionStmt->fetchAll();

    // policz punkty
    foreach ($answers as $ans) {
        if ($ans['is_correct']) {
            $total++;
            $qKey = 'question_' . $ans['qid'];
            if (isset($_POST[$qKey]) && (int)$_POST[$qKey] === (int)$ans['aid']) {
                $score++;
            }
        }
    }

    $bestInfo = '';
    if (isset($_SESSION['user_id'])) {
        $insert = $pdo->prepare("INSERT INTO results (user_id, quiz_id, score, total) VALUES (?, ?, ?, ?)");
        $insert->execute([$_SESSION['user_id'], $quiz_id, $score, $total]);
        // aktualizuj najlepszy wynik w kategorii
        $catStmt = $pdo->prepare("SELECT category_id FROM quizzes WHERE id = ? LIMIT 1");
        $catStmt->execute([$quiz_id]);
        $cat = $catStmt->fetchColumn();
        if ($cat) {
            $bestStmt = $pdo->prepare("SELECT best_score FROM best_scores WHERE user_id=? AND category_id=?");
            $bestStmt->execute([$_SESSION['user_id'], $cat]);
            $best = $bestStmt->fetchColumn();
            if ($best === false) {
                $insBest = $pdo->prepare("INSERT INTO best_scores (user_id, category_id, best_score) VALUES (?, ?, ?)");
                $insBest->execute([$_SESSION['user_id'], $cat, $score]);
                $bestInfo = 'Dodano nowy rekord do best_scores.';
            } elseif ($score > $best) {
                $updBest = $pdo->prepare("UPDATE best_scores SET best_score=? WHERE user_id=? AND category_id=?");
                $updBest->execute([$score, $_SESSION['user_id'], $cat]);
                $bestInfo = 'Zaktualizowano rekord w best_scores.';
            } else {
                $bestInfo = 'Twój wynik nie był lepszy niż dotychczasowy.';
            }
        } else {
            $bestInfo = 'Quiz nie ma przypisanej kategorii.';
        }
    }

    echo "<div class='container'>
            <h1>Wynik quizu: ".htmlspecialchars($quiz['title'])."</h1>
            <p>Twój wynik: <strong>$score / $total</strong></p>
            <p style='color:#00ff99;font-weight:bold;'>$bestInfo</p>
            <a href='quizzes.php' class='button-neon'>Wybierz inny quiz</a>
          </div>";
    include 'partials/footer.php';
    exit;
                $insBest->execute([$_SESSION['user_id'], $cat, $score]);
            } elseif ($score > $best) {
                // jest rekord, aktualizuj jeśli lepszy
                $updBest = $pdo->prepare("UPDATE best_scores SET best_score=? WHERE user_id=? AND category_id=?");
                $updBest->execute([$score, $_SESSION['user_id'], $cat]);
            }
        }
    }

    echo "<div class='container'>
            <h1>Wynik quizu: ".htmlspecialchars($quiz['title'])."</h1>
            <p>Twój wynik: <strong>$score / $total</strong></p>
            <a href='quizzes.php' class='button-neon'>Wybierz inny quiz</a>
          </div>";
    include 'partials/footer.php';
    exit;
}

// Pobierz pytania i odpowiedzi
$stmt = $pdo->prepare("
    SELECT q.id AS qid, q.question_text, a.id AS aid, a.answer_text
    FROM questions q
    JOIN answers a ON a.question_id = q.id
    WHERE q.quiz_id = ?
    ORDER BY q.id
");
$stmt->execute([$quiz_id]);
$rows = $stmt->fetchAll();

// Grupowanie pytań
$questions = [];
foreach ($rows as $row) {
    $qid = $row['qid'];
    if (!isset($questions[$qid])) {
        $questions[$qid] = [
            'text'    => $row['question_text'],
            'answers' => []
        ];
    }
    $questions[$qid]['answers'][] = ['aid'=>$row['aid'], 'text'=>$row['answer_text']];
}

// Tasowanie odpowiedzi
foreach ($questions as &$q) {
    shuffle($q['answers']);
}
unset($q);
?>
<div class="container">
    <h1><?= htmlspecialchars($quiz['title']) ?></h1>
    <form method="POST">
        <?php $idx=1; foreach ($questions as $qid => $q): ?>
            <div style="text-align:center;max-width:800px;margin:0 auto 40px;">
                <h3 style="color:#ffea00;"><?= $idx++ ?>. <?= htmlspecialchars($q['text']) ?></h3>
                <?php foreach ($q['answers'] as $a): ?>
                    <label class="answer-card">
                        <input type="radio" name="question_<?= $qid ?>" value="<?= $a['aid'] ?>" required>
                        <span><?= htmlspecialchars($a['text']) ?></span>
                    </label>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
        <button type="submit" class="button-neon">Zakończ i sprawdź wynik</button>
    </form>
</div>
<?php include __DIR__ . '/partials/footer.php'; ?>
