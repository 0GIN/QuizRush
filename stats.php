
<?php
require_once __DIR__.'/init.php';
include 'partials/header.php';

/* top player per category */
$topStmt=$pdo->query("
SELECT c.name,u.username,u.avatar,s.best_score
FROM best_scores s
JOIN categories c ON c.id=s.category_id
JOIN users u ON u.id=s.user_id
INNER JOIN (
  SELECT category_id, MAX(best_score) AS max_score
  FROM best_scores
  GROUP BY category_id
) bsmax ON bsmax.category_id = s.category_id AND bsmax.max_score = s.best_score
ORDER BY c.name
");
$rows=$topStmt->fetchAll();
?>
<div class="container">
<h1>Tablica wyników</h1>
<?php if(!$rows):?>
<p>Brak danych.</p>
<?php else:?>
<table style="margin:auto;border-collapse:collapse;width:80%;max-width:700px;">
<thead><tr><th>Kategoria</th><th>Gracz</th><th>Wynik</th></tr></thead>
<tbody>
<?php foreach($rows as $r): $ava=$r['avatar']?:'assets/img/default_avatar.png';?>
<tr style="text-align:center;border-bottom:1px solid #444;">
  <td><?= htmlspecialchars($r['name'])?></td>
  <td>
    <img src="<?= htmlspecialchars($ava)?>" style="height:35px;width:35px;border-radius:50%;object-fit:cover;margin-right:8px;vertical-align:middle;">
    <?= htmlspecialchars($r['username'])?>
  </td>
  <td><?= $r['best_score']?></td>
</tr>
<?php endforeach;?>
</tbody>
</table>
<?php endif;?>
</div>
<?php include __DIR__ . '/partials/footer.php'; ?>
