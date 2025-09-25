
<?php
require_once __DIR__ . '/../quizrush_m/init.php';   // startuje sesję jeśli trzeba

$isAdmin    = isset($_SESSION['role']) && $_SESSION['role']==='admin';
$isLogged   = isset($_SESSION['user_id']);
$avatarPath = $_SESSION['avatar'] ?? '/quizrush/assets/img/default_avatar.png';
$username   = $_SESSION['username'] ?? '';
?>
<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8">
  <title>QuizRush</title>
  <link rel="stylesheet" href="/quizrush/assets/css/style.css">
  <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@500&display=swap" rel="stylesheet">
</head>
<body>
<header class="navbar-neon">
  <div class="nav-left">
    <a href="/quizrush/index.php"><img src="/quizrush/assets/img/logo.png" alt="logo" class="logo-img"></a>
    <a class="nav-link" href="/quizrush/index.php">Home</a>
    <a class="nav-link" href="/quizrush/quizrush_m/select_category.php">Kategorie</a>
    <a class="nav-link" href="/quizrush/quizrush_m/stats.php">Statystyki</a>
    <?php if($isLogged): ?>
      <a class="nav-link" href="/quizrush/quizrush_m/suggest_question.php">Dodaj pytanie</a>
    <?php endif; ?>
    <?php if($isAdmin): ?>
      <a class="nav-link" href="/quizrush/admin/queue.php">Panel Admina</a>
    <?php endif; ?>
  </div>
  <div class="nav-right">
    <?php if($isLogged): ?>
  <img src="<?= htmlspecialchars($avatarPath) ?>" class="avatar-img">
  <span><?= htmlspecialchars($username) ?></span>
  <a class="nav-link" href="/quizrush/quizrush_m/profile.php">Mój profil</a>
  <a class="nav-link" href="/quizrush/quizrush_m/logout.php">Wyloguj</a>
    <?php else: ?>
  <a class="nav-link" href="/quizrush/quizrush_m/login.php">Zaloguj</a>
  <a class="nav-link" href="/quizrush/quizrush_m/register.php">Rejestracja</a>
    <?php endif; ?>
  </div>
</header>
