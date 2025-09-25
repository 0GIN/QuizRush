<?php
require_once 'init.php';
$cat = isset($_GET['cat']) ? (int)$_GET['cat'] : 0;
if(!$cat){ header('Location: select_category.php'); exit; }

$key='play_'.$cat;
if(isset($_SESSION[$key]) && $_SESSION[$key]['lives']<=0){
    unset($_SESSION[$key]);
}
if(!isset($_SESSION[$key])){
    $_SESSION[$key]=['lives'=>3,'score'=>0,'answered'=>[]];
}
$g=&$_SESSION[$key];

if($_SERVER['REQUEST_METHOD']==='POST'){
    $aid=(int)$_POST['answer'];
    $qid=(int)$_POST['qid'];
    $elapsed=microtime(true)-($_SESSION['question_start']??microtime(true));
    $ok=$pdo->prepare('SELECT is_correct FROM answers WHERE id=?');
    $ok->execute([$aid]);
    if($ok->fetchColumn()){
        $g['score']+=max(1,100-intval($elapsed*10));
    }else{
        $g['lives']--;
    }
    $g['answered'][]=$qid;
    if($g['lives']<=0){
  $_SESSION['last_score']=$g['score'];
  unset($_SESSION[$key]);
  header('Location: gameover.php?cat='.$cat); exit;
    }
}


$where = 'WHERE c.id=?';
$params = [$cat];
if (!empty($g['answered'])) {
  $in = implode(',', array_fill(0, count($g['answered']), '?'));
  $where .= " AND q.id NOT IN ($in)";
  $params = array_merge($params, $g['answered']);
}

$q=$pdo->prepare("SELECT q.id,q.question_text
                  FROM questions q
                  JOIN quizzes z ON q.quiz_id = z.id
                  JOIN categories c ON z.category_id = c.id
                  $where
                  ORDER BY RAND()
                  LIMIT 1");
$q->execute($params);
$q=$q->fetch();
if(!$q){
  $_SESSION['last_score']=$g['score'];
  unset($_SESSION[$key]);
  header('Location: gameover.php?cat='.$cat); exit;
}
$_SESSION['question_start']=microtime(true);
$ans=$pdo->prepare("SELECT id,answer_text FROM answers WHERE question_id=? ORDER BY RAND()");
$ans->execute([$q['id']]);
$answers=$ans->fetchAll();
include 'partials/header.php';?>
<div class="main-content">
<link rel="stylesheet" href="assets/css/quiz_extra.css">
<div class="container">
  <div style="display:flex;justify-content:space-between;align-items:center;">
    <h2><?=htmlspecialchars($q['question_text'])?></h2>
    <div>
      <div class="hearts"><?php for($i=1;$i<=3;$i++):?><span class="heart <?= $i<=$g['lives']?'':'dead'?>">❤</span><?php endfor;?></div>
      <div style="text-align:right;">Punkty: <strong><?=$g['score']?></strong></div>
    </div>
  </div>
  <form method="POST">
    <div class="answer-grid"><?php foreach($answers as $a):?>
      <button class="answer-btn" type="submit" name="answer" value="<?=$a['id']?>"><?=htmlspecialchars($a['answer_text'])?></button>
    <?php endforeach;?></div>
    <input type="hidden" name="qid" value="<?=$q['id']?>">
  </form>
</div>
<?php include __DIR__ . '/partials/footer.php';?>
</div>