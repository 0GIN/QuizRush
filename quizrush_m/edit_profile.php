
<?php
session_start();
require_once '../config/db.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$user_id = $_SESSION['user_id'];
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $avatar   = trim($_POST['avatar_url']);
    $password = $_POST['password'];

    if (!empty($password)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET username=?, email=?, avatar_url=?, password_hash=? WHERE id=?");
        $stmt->execute([$username, $email, $avatar, $hash, $user_id]);
    } else {
        $stmt = $pdo->prepare("UPDATE users SET username=?, email=?, avatar_url=? WHERE id=?");
        $stmt->execute([$username, $email, $avatar, $user_id]);
    }
    $_SESSION['username'] = $username;
    $msg = "Zaktualizowano dane.";
}

$stmt = $pdo->prepare("SELECT username, email, avatar_url FROM users WHERE id=?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

include '../partials/header.php';
?>
<div class="main-content">
<div class="container">
    <h1>Edytuj profil</h1>
    <?php if ($msg): ?><p style="color:lime"><?= $msg ?></p><?php endif; ?>
    <form class="form-neon" method="POST">
        <label>Nazwa</label>
        <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
        <label>Email</label>
        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
        <label>Nowe hasło</label>
        <input type="password" name="password" placeholder="Zostaw puste by nie zmieniać">
        <label>URL avatara</label>
        <input type="text" name="avatar_url" value="<?= htmlspecialchars($user['avatar_url']) ?>">
        <button class="button-neon">Zapisz</button>
    </form>
</div>
<?php include '../partials/footer.php'; ?>
</div>
