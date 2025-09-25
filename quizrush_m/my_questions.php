<?php
require_once 'init.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); exit;
}
$user_id = $_SESSION['user_id'];

// Pobierz zaakceptowane pytania użytkownika
$stmt = $pdo->prepare("SELECT us.*, c.name as cat FROM user_submissions us LEFT JOIN categories c ON us.category_id = c.id WHERE us.user_id = ? AND us.approved = 1 ORDER BY us.id DESC");
$stmt->execute([$user_id]);
$questions = $stmt->fetchAll();

include 'partials/header.php';
?>
<div class="container">
  <h1 style="text-align:center;">Moje zaakceptowane pytania</h1>
  <?php if (count($questions) === 0): ?>
    <div style="text-align:center;margin-top:60px;font-size:1.3em;">
      <strong>Nie masz jeszcze zaakceptowanych pytań.</strong>
    </div>
  <?php else: ?>
    <?php foreach($questions as $p): ?>
      <div style="margin-bottom:40px;">
        <p><strong>Kategoria:</strong> <?= htmlspecialchars($p['cat']) ?></p>
        <p><strong>Pytanie:</strong> <?= htmlspecialchars($p['question_text']) ?></p>
        <ol>
          <li <?= $p['correct_idx']==1?'style="color:#0f0"':''?>><?= htmlspecialchars($p['answer1']) ?></li>
          <li <?= $p['correct_idx']==2?'style="color:#0f0"':''?>><?= htmlspecialchars($p['answer2']) ?></li>
          <li <?= $p['correct_idx']==3?'style="color:#0f0"':''?>><?= htmlspecialchars($p['answer3']) ?></li>
          <li <?= $p['correct_idx']==4?'style="color:#0f0"':''?>><?= htmlspecialchars($p['answer4']) ?></li>
        </ol>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
</div>
<?php include 'partials/footer.php'; ?>
