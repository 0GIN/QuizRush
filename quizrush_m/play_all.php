<?php
require_once 'init.php';

/* ==== RESET if finished previously ==== */
if(isset($_SESSION['mix']) && $_SESSION['mix']['lives']<=0){
   unset($_SESSION['mix']);
}
/* ==== Init ==== */
if(!isset($_SESSION['mix'])){
   $_SESSION['mix']=['lives'=>3,'score'=>0,'answered'=>[]];
}
$g=&$_SESSION['mix'];

/* ==== Handle answer ==== */
if($_SERVER['REQUEST_METHOD']==='POST'){
  $aid=(int)$_POST['answer'];
  $qid=(int)$_POST['qid'];
  $elapsed=microtime(true)-($_SESSION['question_start']??microtime(true));
  $ok=$pdo->prepare('SELECT is_correct FROM answers WHERE id=?');$ok->execute([$aid]);
  $correct=$ok->fetchColumn();
  if($correct){
      $g['score']+=max(1,100-intval($elapsed*10));
  }else{
      $g['lives']--;
  }
  $g['answered'][]=$qid;
  if($g['lives']<=0){
      $_SESSION['last_score']=$g['score'];
      unset($_SESSION['mix']);
      header('Location: gameover.php');exit;
  }
}

/* ==== get next ==== */
$where='';
$params=[];
if($g['answered']){
 $placeholders=implode(',',array_fill(0,count($g['answered']),'?'));
 $where="WHERE q.id NOT IN ($placeholders)";
 $params=$g['answered'];
}
$q=$pdo->prepare("SELECT q.id,q.question_text FROM questions q $where ORDER BY RAND() LIMIT 1");
$q->execute($params);
$q=$q->fetch();
if(!$q){
   $_SESSION['last_score']=$g['score'];
   unset($_SESSION['mix']);
   header('Location: gameover.php');exit;
}
$_SESSION['question_start']=microtime(true);

$ans=$pdo->prepare('SELECT id,answer_text FROM answers WHERE question_id=? ORDER BY RAND()');
$ans->execute([$q['id']]);
$answers=$ans->fetchAll();

include '../partials/header.php';?>
<style>
  body, html { height: 100%; margin: 0; }
  .main-content { min-height: calc(100vh - 120px); display: flex; flex-direction: column; }
  .container { flex: 1; }
  footer { margin-top: auto; }
</style>
<div class="main-content">
<link rel="stylesheet" href="../assets/css/quiz_extra.css">
<div class="container">
  <div style="display:flex;justify-content:space-between;align-items:center;">
    <h2><?=htmlspecialchars($q['question_text'])?></h2>
    <div>
      <div class="hearts">
        <?php for($i=1;$i<=3;$i++):?><span class="heart <?=$i<=$g['lives']?'':'dead'?>">❤</span><?php endfor;?>
      </div>
      <div style="text-align:right;">Punkty: <strong><?=$g['score']?></strong></div>
    </div>
  </div>
  <form method="POST">
    <div class="answer-grid">
      <?php foreach($answers as $a):?>
        <button class="answer-btn" type="submit" name="answer" value="<?=$a['id']?>"><?=htmlspecialchars($a['answer_text'])?></button>
      <?php endforeach;?>
    </div>
    <input type="hidden" name="qid" value="<?=$q['id']?>">
  </form>
</div>
<?php include '../partials/footer.php'; ?>
</div>
