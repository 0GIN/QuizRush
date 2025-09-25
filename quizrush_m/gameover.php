<?php
require_once 'init.php';
$score = $_SESSION['last_score'] ?? 0;
$cat = isset($_GET['cat']) ? (int)$_GET['cat'] : 0;
unset($_SESSION['last_score']);
$bestInfo = '';
if (isset($_SESSION['user_id']) && $cat) {
  require_once '../config/db.php';
  // Sprawdź osobisty rekord
  $bestStmt = $pdo->prepare("SELECT best_score FROM best_scores WHERE user_id=? AND category_id=?");
  $bestStmt->execute([$_SESSION['user_id'], $cat]);
  $personalBest = $bestStmt->fetchColumn();
  $personalRecordUpdated = false;
  if ($personalBest === false) {
    $insBest = $pdo->prepare("INSERT INTO best_scores (user_id, category_id, best_score) VALUES (?, ?, ?)");
    $insBest->execute([$_SESSION['user_id'], $cat, $score]);
    $bestInfo = 'Ustanowiłeś swój pierwszy rekord w tej kategorii!<br>Rekord zapisano na stronie profilu.';
    $personalRecordUpdated = true;
  } elseif ($score > $personalBest) {
    $updBest = $pdo->prepare("UPDATE best_scores SET best_score=? WHERE user_id=? AND category_id=?");
    $updBest->execute([$score, $_SESSION['user_id'], $cat]);
    $bestInfo = 'Pobito Twój osobisty rekord w tej kategorii!<br>Rekord zapisano na stronie profilu.';
    $personalRecordUpdated = true;
  } else {
    $bestInfo = 'Twój wynik nie był lepszy niż Twój dotychczasowy rekord.';
  }

  // Sprawdź czy to jest rekord ogólny
  $globalStmt = $pdo->prepare("SELECT MAX(best_score) FROM best_scores WHERE category_id=?");
  $globalStmt->execute([$cat]);
  $globalBest = $globalStmt->fetchColumn();
  // Pobierz użytkownika z rekordem ogólnym
  $globalUserStmt = $pdo->prepare("SELECT user_id FROM best_scores WHERE category_id=? AND best_score=?");
  $globalUserStmt->execute([$cat, $globalBest]);
  $globalUserId = $globalUserStmt->fetchColumn();
  if ($score == $globalBest && $globalUserId == $_SESSION['user_id']) {
    if ($personalRecordUpdated) {
      $bestInfo .= '<br><span style="color:#ff00ff">To także nowy rekord ogólny!<br>Rekord zapisano na tablicy statystyk.</span>';
    } else {
      $bestInfo = 'Twój wynik wyrównał rekord ogólny w tej kategorii!<br>Rekord zapisano na tablicy statystyk.';
    }
  }
}
include '../partials/header.php';?>
<div class="container" style="text-align:center">
  <h1>Koniec gry!</h1>
  <p>Twój wynik: <strong><?=$score?></strong></p>
  <?php if($bestInfo): ?><p style="color:#00ff99;font-weight:bold;"><?=$bestInfo?></p><?php endif; ?>
  <?php if($cat): ?>
    <a href="play.php?cat=<?=$cat?>&restart=1"><button class="button-neon">Zagraj ponownie</button></a>
  <?php else: ?>
    <a href="play_all.php?restart=1"><button class="button-neon">Zagraj ponownie</button></a>
  <?php endif; ?>
  <br><br>
  <a href="/quizrush/index.php"><button class="button-neon">Strona główna</button></a>
</div>
<?php include '../partials/footer.php'; ?>
