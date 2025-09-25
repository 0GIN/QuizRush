
<?php
session_start();
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
$isLoggedIn = isset($_SESSION['user_id']);
$avatarPath = $_SESSION['avatar'] ?? '../assets/default_avatar.png';
$username = $_SESSION['username'] ?? '';
?>
<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8">
  <title>QuizRush</title>
  <link rel="stylesheet" href="../assets/style.css">
  <link rel="icon" type="image/png" href="../assets/logo.png">
</head>
<body>
  <header class="navbar-neon">
    <div class="nav-left">
      <a href="index.php" class="logo-link">
  <img src="../assets/logo.png" alt="QuizRush" class="logo-img">
      </a>
  <a href="../index.php" class="nav-link">Home</a>
  <a href="categories.php" class="nav-link">Kategorie</a>
  <a href="stats.php" class="nav-link">Statystyki</a>
      <?php if ($isLoggedIn): ?>
  <a href="suggest_question.php" class="nav-link">Dodaj pytanie</a>
      <?php endif; ?>
      <?php if ($isAdmin): ?>
  <a href="admin_panel.php" class="nav-link">Admin panel</a>
      <?php endif; ?>
    </div>
    <div class="nav-right">
      <?php if ($isLoggedIn): ?>
  <img src="<?= htmlspecialchars($avatarPath) ?>" alt="avatar" class="avatar-img">
  <span class="user-name"><?= htmlspecialchars($username) ?></span>
  <a href="profile.php" class="nav-link">Mój profil</a>
  <a href="logout.php" class="nav-link">Wyloguj</a>
      <?php else: ?>
  <a href="login.php" class="nav-link">Zaloguj</a>
  <a href="register.php" class="nav-link">Zarejestruj</a>
      <?php endif; ?>
    </div>
  </header>
