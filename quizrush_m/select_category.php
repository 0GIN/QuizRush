<?php
require_once __DIR__.'/init.php';
include __DIR__ . '/../partials/header.php';
require_once __DIR__.'/../config/db.php';
$pdo = require __DIR__.'/../config/db.php';
$cats = $pdo->query("SELECT id,name FROM categories ORDER BY name")->fetchAll();
?>
<div class="container">
 <h1>Kategorie quizów</h1>
 <div style="display:flex;flex-wrap:wrap;gap:25px;justify-content:center;">
 <?php foreach($cats as $c): ?>
   <?php if(isset($_SESSION['user_id'])): ?>
     <a href="play.php?cat=<?= $c['id'] ?>" class="button-neon" style="min-width:200px;"><?= htmlspecialchars($c['name']) ?></a>
   <?php else: ?>
     <div style="min-width:200px;padding:15px 25px;border:2px solid #00fff7;border-radius:12px;text-align:center;opacity:0.5;">
         <?= htmlspecialchars($c['name']) ?>
     </div>
   <?php endif; ?>
 <?php endforeach;?>
 </div>
 <?php if(!isset($_SESSION['user_id'])): ?>
   <p style="text-align:center;margin-top:40px;">Jeśli chcesz wybrać kategorię i zapisywać swoje postępy, <strong>załóż konto</strong>.</p>
   <div style="text-align:center;">
     <a href="register.php"><button class="button-neon">Rejestracja</button></a>
   </div>
 <?php endif;?>
</div>
<?php include __DIR__ . '/../partials/footer.php'; ?>