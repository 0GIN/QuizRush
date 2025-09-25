<?php require_once __DIR__ . '/quizrush_m/init.php'; include __DIR__ . '/partials/header.php'; ?>
<div class="main-content">
  <div class="container" style="text-align:center;padding:90px 20px;">
    <h1>Witamy w QuizRush!</h1>
    <p style="font-size:24px;margin:20px 0 40px;">Przetestuj swoją wiedzę</p>
  <a href="quizrush_m/play_all.php"><button class="button-neon">Zacznij quiz</button></a>
  <p style="font-size:12px;color:#888;margin-top:18px;max-width:400px;margin-left:auto;margin-right:auto;">
    <strong>Informacja:</strong> Punkty są liczone według wzoru <code>max(1, 100 - czas_na_odpowiedź_w_sekundach * 10)</code>.<br>
    Im szybciej odpowiesz, tym więcej punktów zdobywasz. Za złą odpowiedź tracisz jedno serce.
  </p>
  </div>
</div>
<?php include __DIR__ . '/partials/footer.php'; ?>