
<?php
session_start();
require_once '../config/db.php';
include '../partials/header.php';

$category_id = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;

// kategorii
$cats = $pdo->query("SELECT id, name FROM categories ORDER BY name")->fetchAll();

// pobierz quizy z (opcjonalnym) filtrem
$where = "";
$params = [];
if ($category_id) {
    $where = "WHERE q.category_id = ?";
    $params[] = $category_id;
}

$stmt = $pdo->prepare("
    SELECT q.id, q.title, q.description, IFNULL(c.name,'Brak') AS category
    FROM quizzes q
    LEFT JOIN categories c ON c.id = q.category_id
    $where
    ORDER BY q.created_at DESC
");
$stmt->execute($params);
$quizzes = $stmt->fetchAll();
?>
<div class="container">
    <h1>Quizy</h1>

    <!-- Filtr kategorii -->
    <form method="GET" style="margin:20px 0;">
        <select name="category_id" onchange="this.form.submit()" class="button-neon" style="padding:10px 20px;font-size:18px;">
            <option value="0">Wszystkie kategorie</option>
            <?php foreach ($cats as $cat): ?>
                <option value="<?= $cat['id'] ?>" <?= $category_id==$cat['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>

    <?php if (!$quizzes): ?>
        <p>Brak quizów w tej kategorii.</p>
    <?php else: ?>
        <div style="display:flex;flex-wrap:wrap;justify-content:center;gap:30px;">
        <?php foreach ($quizzes as $q): ?>
            <div style="background:#1a1a2e;border-radius:15px;padding:20px;width:300px;box-shadow:0 0 15px #00fff7;">
                <h3 style="color:#ffea00;margin-top:0;"><?= htmlspecialchars($q['title']) ?></h3>
                <p style="min-height:60px"><?= nl2br(htmlspecialchars($q['description'])) ?></p>
                <p><strong>Kategoria:</strong> <?= htmlspecialchars($q['category']) ?></p>
                <a href="quiz.php?id=<?= $q['id'] ?>" class="button-neon">Start</a>
            </div>
        <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
<?php include '../partials/footer.php'; ?>
