
<?php
require_once __DIR__ . '/init.php';
include '../partials/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];
$success = $error = "";

// Funkcja walidująca hasło
function validate_password($password) {
    $errors = [];
    if (strlen($password) > 0 && strlen($password) < 8) $errors[] = "Hasło musi mieć minimum 8 znaków.";
    if (strlen($password) > 20) $errors[] = "Hasło może mieć maksymalnie 20 znaków.";
    if (strlen($password) > 0 && !preg_match('/[A-Z]/', $password)) $errors[] = "Hasło musi zawierać wielką literę.";
    if (strlen($password) > 0 && !preg_match('/[a-z]/', $password)) $errors[] = "Hasło musi zawierać małą literę.";
    if (strlen($password) > 0 && !preg_match('/[0-9]/', $password)) $errors[] = "Hasło musi zawierać cyfrę.";
    if (strlen($password) > 0 && !preg_match('/[^A-Za-z0-9]/', $password)) $errors[] = "Hasło musi zawierać znak specjalny.";
    return $errors;
}

$passwordErrors = [];
$emailError = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $passwordErrors = validate_password($password);
    if (!preg_match('/^[^@\s]+@[^@\s]+\.[^@\s]+$/', $email)) {
        $emailError = 'E-mail musi być w formacie: tekst@tekst.tekst';
    }
    $uploadRelDir = '/quizrush/uploads/';
    $uploadAbsDir = $_SERVER['DOCUMENT_ROOT'] . $uploadRelDir;
    if (!is_dir($uploadAbsDir)) mkdir($uploadAbsDir, 0755, true);

    if (empty($passwordErrors) && empty($emailError)) {
        // Avatar upload
        $avatarPath = null;
        if (!empty($_FILES['avatar']['name']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg','jpeg','png','gif'])) {
                $fileRel  = $uploadRelDir . uniqid('ava_'.$userId.'_') . '.' . $ext;
                $fileAbs  = $_SERVER['DOCUMENT_ROOT'] . $fileRel;
                if (move_uploaded_file($_FILES['avatar']['tmp_name'], $fileAbs)) {
                    $avatarPath = $fileRel;
                    $_SESSION['avatar'] = $avatarPath;
                } else {
                    $error = "Nie udało się zapisać pliku.";
                }
            } else {
                $error = "Dozwolone formaty: jpg, png, gif.";
            }
        }

        // Update DB
        if (!$error) {
            $sql = "UPDATE users SET username=?, email=?";
            $params = [$username, $email];
            if (!empty($password)) {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $sql .= ", password_hash=?";
                $params[] = $hash;
            }
            if ($avatarPath) { $sql .= ", avatar=?"; $params[] = $avatarPath; }
            $sql .= " WHERE id=?";
            $params[] = $userId;
            $pdo->prepare($sql)->execute($params);
            $_SESSION['username'] = $username;
            $success = "Dane zostały zaktualizowane.";
        }
    }
}

// fetch user
$stmt = $pdo->prepare("SELECT username,email,IFNULL(avatar,'') avatar FROM users WHERE id=?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

// fetch best scores
$rows = $pdo->prepare("
 SELECT c.name, s.best_score
 FROM best_scores s
 JOIN categories c ON c.id=s.category_id
 WHERE s.user_id=?
 ORDER BY c.name");
$rows->execute([$userId]);
$scores = $rows->fetchAll();

$avatarUrl = $user['avatar'] ?: '/quizrush/assets/img/default_avatar.png';
?>
<div class="main-content">
<div class="container">
  <h1 class="h3 mb-4 text-gray-800" style="text-align:center;color:#ff00ff;text-shadow:0 0 8px #ff00ff;">Mój profil</h1>

  <?php if ($success): ?><p style="color:#00ff99;text-align:center;"><?= $success ?></p><?php endif; ?>
  <?php if ($error): ?><p style="color:#ff5555;text-align:center;"><?= $error ?></p><?php endif; ?>

  <div style="display:flex;flex-wrap:wrap;justify-content:center;gap:40px;">
    <div>
      <img src="<?= htmlspecialchars($avatarUrl) ?>" style="width:120px;height:120px;border-radius:50%;object-fit:cover;box-shadow:0 0 15px #00fff7;">
    </div>

    <form method="POST" enctype="multipart/form-data" class="form-neon" style="max-width:400px;width:100%;">
      <label>Pseudonim</label>
      <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>

      <label>E‑mail</label>
      <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
      <div id="email-error" style="margin-top:2px;margin-bottom:8px;">
      <?php if (!empty($emailError)) {
          echo '<span class="password-error">'.$emailError.'</span>';
      } ?>
      </div>

      <label>Nowe hasło (opcjonalnie)</label>
      <input type="password" name="password" placeholder="*****">
      <div id="password-errors" style="margin-top:2px;margin-bottom:8px;">
      <?php if (!empty($passwordErrors)) {
          echo '<span class="password-error">'.implode(', ', $passwordErrors).'</span>';
      } ?>
      </div>

      <label>Awatar (JPG/PNG/GIF)</label>
      <input type="file" name="avatar" accept=".jpg,.jpeg,.png,.gif">

      <button class="button-neon" style="width:100%;margin-top:15px;">Zapisz zmiany</button>
    </form>
  </div>


    <h2 style="margin-top:50px;color:#00fff7;text-shadow:0 0 6px #00fff7;">Najlepsze wyniki w kategoriach</h2>
    <?php if ($scores): ?>
        <ul style="list-style:none;padding:0;text-align:center;font-size:18px;">
            <?php foreach ($scores as $s): ?>
                <li><?= htmlspecialchars($s['name']) ?>: <strong><?= $s['best_score'] ?></strong> pkt</li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p style="text-align:center;">Brak wyników.</p>
    <?php endif; ?>

    <h2 style="margin-top:50px;color:#00fff7;text-shadow:0 0 6px #00fff7;">Moje zaakceptowane pytania</h2>
    <?php
    // Pobierz zaakceptowane pytania użytkownika
    $stmt = $pdo->prepare("SELECT us.*, c.name as cat FROM user_submissions us LEFT JOIN categories c ON us.category_id = c.id WHERE us.user_id = ? AND us.approved = 1 ORDER BY us.id DESC");
    $stmt->execute([$userId]);
    $questions = $stmt->fetchAll();
    ?>
    <?php if (count($questions) === 0): ?>
        <div style="text-align:center;margin-top:30px;font-size:1.1em;">
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
    </div>
</div>
<?php include '../partials/footer.php'; ?>
