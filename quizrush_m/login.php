<?php
require_once 'init.php';
$pdo = require __DIR__.'/../config/db.php';

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';

    if ($email === '' || $pass === '') {
        $error = "Wprowadź e‑mail i hasło.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($pass, $user['password_hash'])) {
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['username']  = $user['username'];
            $_SESSION['role']      = $user['role'];
            $_SESSION['avatar']    = $user['avatar'];
            header("Location: ../index.php");
            exit;
        } else {
            $error = "Nieprawidłowy e‑mail lub hasło.";
        }
    }
}
?>

<?php include '../partials/header.php'; ?>
<div class="container" style="max-width:400px;margin:auto;padding:40px 0;">
    <h1 style="text-align:center;margin-bottom:30px;">Logowanie</h1>
    <?php if ($error): ?>
        <p style="color:red;text-align:center;"><?= $error ?></p>
    <?php endif; ?>
    <form method="post" class="form-neon">
        <label>E‑mail</label>
        <input type="email" name="email" required placeholder="email@example.com">

        <label>Hasło</label>
        <input type="password" name="password" required placeholder="Hasło">

        <button class="button-neon" style="width:100%;margin-top:20px;">Zaloguj</button>
    </form>
    <p style="text-align:center;margin-top:15px;">Nie masz konta?
        <a href="register.php">Zarejestruj się</a>
    </p>
</div>
<?php include '../partials/footer.php'; ?>
