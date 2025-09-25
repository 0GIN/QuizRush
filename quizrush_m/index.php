<?php
require_once 'init.php';
include '../partials/header.php';
?>
<div class="container" style="text-align:center">
  <h1>QuizRush</h1>
  <p>Witaj w aplikacji quizowej!<br>Wybierz kategorię lub tryb mieszany, aby rozpocząć grę.</p>
  <a href="select_category.php"><button class="button-neon">Kategorie</button></a>
  <a href="play_all.php"><button class="button-neon">Tryb mieszany</button></a>
  <br><br>
  <p style="color:#00ff99;font-weight:bold;">Punkty są liczone według czasu odpowiedzi i poprawności.</p>
</div>
<?php include '../partials/footer.php'; ?>
