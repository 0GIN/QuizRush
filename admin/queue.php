<?php
require_once __DIR__.'/../quizrush_m/init.php';
$pdo = require __DIR__.'/../config/db.php';
if ($_SESSION['role'] !== 'admin') { header('Location: /quizrush/index.php'); exit; }

// Dodanie menu na górze
?>
<?php include __DIR__ . '/../partials/header.php'; ?>
<div class="main-content">
<div class="container">
  <nav style="display:flex;justify-content:center;gap:30px;margin-bottom:30px;">
  <a href="queue.php?view=users" class="button-neon" style="padding:12px 28px;">Zarządzanie użytkownikami</a>
  <a href="queue.php?view=questions" class="button-neon" style="padding:12px 28px;">Pytania do zatwierdzenia</a>
  <a href="queue.php?view=edit" class="button-neon" style="padding:12px 28px;;">Edytuj pytania</a>
  </nav>
<?php
// Obsługa edycji użytkownika
if (isset($_POST['edit_user'])) {
    $id = (int)$_POST['id'];
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $role = ($_POST['role'] === 'admin') ? 'admin' : 'user';
    $nickname = trim($_POST['nickname']);
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $stmt = $pdo->prepare("UPDATE users SET username=?, email=?, role=?, nickname=?, is_active=? WHERE id=?");
    $stmt->execute([$username, $email, $role, $nickname, $is_active, $id]);
    $_SESSION['msg'] = 'Zaktualizowano dane użytkownika.';
}

// Wyszukiwanie i sortowanie
$search = isset($_GET['search']) ? $_GET['search'] : '';
$allowedSort = ['id','username','email','role','is_active','nickname'];
$sort = isset($_GET['sort']) && in_array($_GET['sort'],$allowedSort) ? $_GET['sort'] : 'id';
$order = isset($_GET['order']) && strtolower($_GET['order'])=='desc' ? 'DESC' : 'ASC';
$query = "SELECT * FROM users WHERE username LIKE ? ORDER BY $sort $order";
$users = $pdo->prepare($query);
$users->execute(['%'.$search.'%']);
$users = $users->fetchAll();

// Obsługa akceptacji/odrzucenia zgłoszenia
if (isset($_GET['action']) && isset($_GET['id'])) {
  $id = (int)$_GET['id'];
  if ($_GET['action'] === 'accept') {
    // Pobierz zgłoszenie
    $stmt = $pdo->prepare("SELECT * FROM user_submissions WHERE id=?");
    $stmt->execute([$id]);
    $sub = $stmt->fetch();
    if ($sub) {
      // Znajdź quiz dla kategorii
      $stmtQuiz = $pdo->prepare("SELECT id FROM quizzes WHERE category_id = ? LIMIT 1");
      $stmtQuiz->execute([$sub['category_id']]);
      $quiz = $stmtQuiz->fetch();
      if (!$quiz) {
        // Utwórz quiz automatycznie dla kategorii
        $stmtNewQuiz = $pdo->prepare("INSERT INTO quizzes (title, description, category_id) VALUES (?, ?, ?)");
        $catName = $pdo->query("SELECT name FROM categories WHERE id=" . (int)$sub['category_id'])->fetchColumn();
        $stmtNewQuiz->execute([
          $catName,
          'Quiz utworzony automatycznie dla kategorii',
          $sub['category_id']
        ]);
        $quizId = $pdo->lastInsertId();
      } else {
        $quizId = $quiz['id'];
      }
      // Dodaj pytanie do puli pytań
      $stmtQ = $pdo->prepare("INSERT INTO questions (quiz_id, question_text) VALUES (?, ?)");
      $stmtQ->execute([$quizId, $sub['question_text']]);
      $newQid = $pdo->lastInsertId();
      // Dodaj odpowiedzi
      for ($i = 1; $i <= 4; $i++) {
        $is_correct = ($sub['correct_idx'] == $i) ? 1 : 0;
        $stmtA = $pdo->prepare("INSERT INTO answers (question_id, answer_text, is_correct) VALUES (?, ?, ?)");
        $stmtA->execute([$newQid, $sub['answer'.$i], $is_correct]);
      }
      // Oznacz zgłoszenie jako zatwierdzone
      $stmt = $pdo->prepare("UPDATE user_submissions SET approved=1 WHERE id=?");
      $stmt->execute([$id]);
    }
    $_SESSION['msg'] = 'Pytanie zostało zatwierdzone i dodane do puli.';
    header('Location: queue.php?view=questions');
    exit;
  }
  // Możesz dodać else dla odrzucenia zgłoszenia jeśli potrzeba
}
$view = $_GET['view'] ?? 'users';

// Obsługa edycji pytania
if (isset($_POST['edit_question'])) {
  $qid = (int)$_POST['qid'];
  $question_text = trim($_POST['question_text']);
  $new_category_id = isset($_POST['new_category_id']) ? (int)$_POST['new_category_id'] : null;
  if ($new_category_id) {
      // Pobierz quiz powiązany z nową kategorią
      $stmtQuiz = $pdo->prepare("SELECT id FROM quizzes WHERE category_id=? LIMIT 1");
      $stmtQuiz->execute([$new_category_id]);
      $quiz = $stmtQuiz->fetch();
      if ($quiz) {
          $stmt = $pdo->prepare("UPDATE questions SET question_text=?, quiz_id=? WHERE id=?");
          $stmt->execute([$question_text, $quiz['id'], $qid]);
      } else {
          $stmt = $pdo->prepare("UPDATE questions SET question_text=? WHERE id=?");
          $stmt->execute([$question_text, $qid]);
      }
  } else {
      $stmt = $pdo->prepare("UPDATE questions SET question_text=? WHERE id=?");
      $stmt->execute([$question_text, $qid]);
  }
  // Aktualizuj odpowiedzi
  if (isset($_POST['answers']) && is_array($_POST['answers'])) {
      foreach ($_POST['answers'] as $aid => $atext) {
          $stmt = $pdo->prepare("UPDATE answers SET answer_text=? WHERE id=?");
          $stmt->execute([trim($atext), $aid]);
      }
  }
  // Ustaw poprawną odpowiedź
  if (isset($_POST['correct_id'])) {
      $correct_id = (int)$_POST['correct_id'];
      // Najpierw wyzeruj wszystkie
      $stmt = $pdo->prepare("UPDATE answers SET is_correct=0 WHERE question_id=?");
      $stmt->execute([$qid]);
      // Ustaw wybraną jako poprawną
      $stmt = $pdo->prepare("UPDATE answers SET is_correct=1 WHERE id=?");
      $stmt->execute([$correct_id]);
  }
  $_SESSION['msg'] = 'Pytanie zostało zaktualizowane.';
  header('Location: queue.php?view=edit&cat=' . (isset($_GET['cat']) ? $_GET['cat'] : ''));
  exit;
}
$view = $_GET['view'] ?? 'users';
if ($view === 'users') {
  // ...zarządzanie użytkownikami...
  ?>
  <h2 style="text-align:center;">Panel admina - Zarządzanie użytkownikami</h2>
  <form method="get" style="margin-bottom:20px;">
    <input type="hidden" name="view" value="users">
    <input type="text" name="search" placeholder="Szukaj po nazwie" value="<?= htmlspecialchars($search) ?>">
    <button type="submit">Szukaj</button>
  </form>
  <table border="1">
    <tr>
      <th><a href="?view=users&sort=id&order=<?= $order=='ASC'?'desc':'asc' ?>">ID</a></th>
      <th><a href="?view=users&sort=username&order=<?= $order=='ASC'?'desc':'asc' ?>">Login</a></th>
      <th><a href="?view=users&sort=email&order=<?= $order=='ASC'?'desc':'asc' ?>">Email</a></th>
      <th><a href="?view=users&sort=role&order=<?= $order=='ASC'?'desc':'asc' ?>">Rola</a></th>
      <th><a href="?view=users&sort=is_active&order=<?= $order=='ASC'?'desc':'asc' ?>">Aktywny</a></th>
      <th><a href="?view=users&sort=nickname&order=<?= $order=='ASC'?'desc':'asc' ?>">Nick</a></th>
      <th>Akcje</th>
    </tr>
    <?php foreach($users as $u): ?>
    <tr>
      <form method="post">
        <td><?= $u['id'] ?><input type="hidden" name="id" value="<?= $u['id'] ?>"></td>
        <td><input type="text" name="username" value="<?= htmlspecialchars($u['username']) ?>"></td>
        <td><input type="email" name="email" value="<?= htmlspecialchars($u['email']) ?>"></td>
        <td>
          <select name="role">
            <option value="admin" <?= $u['role']=='admin'?'selected':'' ?>>admin</option>
            <option value="user" <?= $u['role']=='user'?'selected':'' ?>>user</option>
          </select>
        </td>
        <td><input type="hidden" name="is_active" value="0"><input type="checkbox" name="is_active" value="1" <?= $u['is_active']?'checked':'' ?>></td>
        <td><input type="text" name="nickname" value="<?= htmlspecialchars($u['nickname']) ?>"></td>
        <td><button type="submit" name="edit_user">Zapisz</button></td>
      </form>
    </tr>
    <?php endforeach; ?>
  </table>
  <?php
} elseif ($view === 'questions') {
  // Pobierz pytania do zatwierdzenia
  // Jeśli nie masz kolumny 'status' w user_submissions, usuń warunek WHERE us.status = 'pending'
  $questions = $pdo->query("SELECT us.*, c.name AS cat, u.username FROM user_submissions us JOIN categories c ON us.category_id = c.id JOIN users u ON us.user_id = u.id WHERE us.approved IS NULL OR us.approved = 0 ORDER BY us.id DESC")->fetchAll();
  ?>
  <h2 style="text-align:center;margin-top:40px;margin-bottom:30px;">Pytania do zatwierdzenia</h2>
  <?php if (count($questions) === 0): ?>
    <div style="text-align:center;margin-top:60px;font-size:1.3em;">
      <strong>Brak pytań do zatwierdzenia.</strong>
    </div>
  <?php else: ?>
    <?php foreach($questions as $p): ?>
      <div>
        <p><strong>Kategoria:</strong> <?= htmlspecialchars($p['cat']) ?> |
           <strong>Autor:</strong> <?= htmlspecialchars($p['username']) ?>
        </p>
        <p><strong>Pytanie:</strong> <?= htmlspecialchars($p['question_text']) ?></p>
        <ol>
          <li <?= $p['correct_idx']==1?'style="color:#0f0"':''?>><?= htmlspecialchars($p['answer1']) ?></li>
          <li <?= $p['correct_idx']==2?'style="color:#0f0"':''?>><?= htmlspecialchars($p['answer2']) ?></li>
          <li <?= $p['correct_idx']==3?'style="color:#0f0"':''?>><?= htmlspecialchars($p['answer3']) ?></li>
          <li <?= $p['correct_idx']==4?'style="color:#0f0"':''?>><?= htmlspecialchars($p['answer4']) ?></li>
        </ol>
        <a href="?view=questions&action=accept&id=<?= $p['id'] ?>" class="button-neon" style="margin-right:10px;background:#00ff99;">Akceptuj</a>
        <a href="?view=questions&action=reject&id=<?= $p['id'] ?>" class="button-neon" style="background:#ff0066;">Odrzuć</a>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
  <?php
} elseif ($view === 'edit') {
  // Panel edycji pytań
  // Pobierz kategorie
  $categories = $pdo->query("SELECT id, name FROM categories ORDER BY name")->fetchAll();
  $selected_cat = isset($_GET['cat']) ? (int)$_GET['cat'] : 0;
  $keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
  // Panel edycji pytań: domyślnie wszystkie, potem filtracja
  if ($selected_cat) {
    // Pobierz quizy z wybranej kategorii
    $quizIds = $pdo->prepare("SELECT id FROM quizzes WHERE category_id=?");
    $quizIds->execute([$selected_cat]);
    $quizIds = $quizIds->fetchAll(PDO::FETCH_COLUMN);
    if (count($quizIds) > 0) {
        $in = implode(',', array_fill(0, count($quizIds), '?'));
        $sql = "SELECT * FROM questions WHERE quiz_id IN ($in) ORDER BY id DESC";
        $questions = $pdo->prepare($sql);
        $questions->execute($quizIds);
        $questions = $questions->fetchAll();
    } else {
        $questions = [];
    }
  } elseif ($keyword) {
    $sql = "SELECT * FROM questions WHERE question_text LIKE ? ORDER BY id DESC";
    $kw = "%$keyword%";
    $params = [$kw];
    $questions = $pdo->prepare($sql);
    $questions->execute($params);
    $questions = $questions->fetchAll();
  } else {
    $questions = $pdo->query("SELECT * FROM questions ORDER BY id DESC")->fetchAll();
  }
  ?>
  <h2 style="text-align:center;margin-top:40px;margin-bottom:30px;">Edytuj pytania</h2>
  <form method="get" style="display:flex;justify-content:center;gap:20px;margin-bottom:30px;">
    <input type="hidden" name="view" value="edit">
    <select name="cat" onchange="this.form.submit()">
      <option value="0">-- Wybierz kategorię --</option>
      <?php foreach($categories as $cat): ?>
        <option value="<?= $cat['id'] ?>" <?= $selected_cat==$cat['id']?'selected':'' ?>><?= htmlspecialchars($cat['name']) ?></option>
      <?php endforeach; ?>
    </select>
    <input type="text" name="keyword" placeholder="Szukaj po słowie w pytaniu lub odpowiedzi" value="<?= htmlspecialchars($keyword) ?>">
    <button type="submit">Szukaj</button>
  </form>
  <?php if (count($questions) === 0): ?>
    <div style="text-align:center;margin-top:60px;font-size:1.3em;">
      <strong>Brak pytań do wyświetlenia.</strong>
    </div>
  <?php else: ?>
    <div style="max-width:900px;margin:auto;">
      <?php foreach($questions as $q): ?>
      <?php
        $answers = $pdo->prepare("SELECT * FROM answers WHERE question_id=? ORDER BY id ASC");
        $answers->execute([$q['id']]);
        $answers = $answers->fetchAll();
      ?>
      <form method="post" style="background:#181030;border:2px solid #44f;padding:18px;margin-bottom:18px;border-radius:10px;display:flex;flex-wrap:wrap;align-items:center;gap:18px;">
        <div style="min-width:40px;font-weight:bold;">ID: <?= $q['id'] ?><input type="hidden" name="qid" value="<?= $q['id'] ?>"></div>
        <div style="flex:1;min-width:220px;">
          <label>Pytanie:<br>
            <input type="text" name="question_text" value="<?= htmlspecialchars($q['question_text']) ?>" style="width:100%;max-width:400px;">
          </label>
          <label>Kategoria:<br>
            <select name="new_category_id" style="width:100%;max-width:200px;">
              <?php foreach($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>" <?= (isset($q['quiz_id']) && $cat['id'] == getCategoryIdForQuiz($q['quiz_id'], $pdo)) ? 'selected' : '' ?>><?= htmlspecialchars($cat['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </label>
        </div>
        <div style="flex:2;min-width:320px;">
          <label>Odpowiedzi:<br>
            <?php foreach($answers as $a): ?>
              <input type="text" name="answers[<?= $a['id'] ?>]" value="<?= htmlspecialchars($a['answer_text']) ?>" style="width:120px;">
            <?php endforeach; ?>
          </label>
        </div>
        <div>
          <label>Poprawna:
            <select name="correct_id">
              <?php foreach($answers as $a): ?>
                <option value="<?= $a['id'] ?>" <?= $a['is_correct'] ? 'selected' : '' ?>><?= htmlspecialchars($a['answer_text']) ?></option>
              <?php endforeach; ?>
            </select>
          </label>
        </div>
        <div style="display:flex;flex-direction:column;gap:8px;">
          <button type="submit" name="edit_question" style="padding:8px 22px;background:#00ff99;color:#222;border-radius:6px;border:none;font-weight:bold;">Zapisz</button>
          <button type="submit" name="delete_question" style="padding:8px 22px;background:#ff0066;color:#fff;border-radius:6px;border:none;font-weight:bold;" onclick="return confirm('Na pewno usunąć pytanie i odpowiedzi?')">Usuń</button>
        </div>
      </form>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
  <?php
}
?>
  </div>
</div>
<?php include __DIR__ . '/../partials/footer.php'; ?>
<?php
function getCategoryIdForQuiz($quiz_id, $pdo) {
    $stmt = $pdo->prepare("SELECT category_id FROM quizzes WHERE id=? LIMIT 1");
    $stmt->execute([$quiz_id]);
    $row = $stmt->fetch();
    return $row ? $row['category_id'] : 0;
}
?>