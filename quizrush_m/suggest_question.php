<?php
require_once 'init.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); exit;
}

$cats = $pdo->query("SELECT id,name FROM categories ORDER BY name")->fetchAll();
$info = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cat  = (int)$_POST['category_id'];
    $q    = trim($_POST['question']);
    $a1   = trim($_POST['a1']);
    $a2   = trim($_POST['a2']);
    $a3   = trim($_POST['a3']);
    $a4   = trim($_POST['a4']);
    $corr = (int)$_POST['correct'];

    if ($q && $a1 && $a2 && $a3 && $a4 && in_array($corr,[1,2,3,4])) {
        $ins = $pdo->prepare(
            "INSERT INTO user_submissions
             (user_id, category_id, question_text, answer1, answer2, answer3, answer4, correct_idx)
             VALUES (?,?,?,?,?,?,?,?)");
        $ins->execute([$_SESSION['user_id'],$cat,$q,$a1,$a2,$a3,$a4,$corr]);
        $info = "Dziękujemy! Pytanie trafiło do kolejki.";
    } else {
        $info = "Wypełnij wszystkie pola i zaznacz poprawną odpowiedź!";
    }
}

include '../partials/header.php';
?>
<div class="main-content">
<div class="container">
  <h1>Dodaj własne pytanie</h1>
  <?php if ($info): ?><p style="color:#0ff"><?= $info ?></p><?php endif; ?>
  <form method="POST" class="form-neon" style="max-width:600px;margin:auto;">
    <label>Kategoria</label>
    <select name="category_id" required>
      <?php foreach ($cats as $c): ?>
        <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
      <?php endforeach; ?>
    </select>

    <label>Treść pytania</label>
    <textarea name="question" required rows="3"></textarea>

    <label>Odpowiedzi</label>
    <?php for ($i=1;$i<=4;$i++): ?>
      <input type="text" name="a<?= $i ?>" placeholder="Odpowiedź <?= $i ?>" required>
      <label style="display:inline">
        <input type="radio" name="correct" value="<?= $i ?>" <?= $i==1?'checked':'' ?>> poprawna?
      </label><br>
    <?php endfor; ?>

    <button class="button-neon" style="margin-top:15px">Wyślij do kolejki</button>
  </form>
</div>
<?php include '../partials/footer.php'; ?>
</div>