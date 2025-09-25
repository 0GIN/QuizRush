
<?php
$pdo = require_once __DIR__ . '/../config/db.php';
$message = '';
// Funkcja walidująca hasło
function validate_password($password) {
    $errors = [];
    if (strlen($password) < 8) $errors[] = "Hasło musi mieć minimum 8 znaków.";
    if (strlen($password) > 20) $errors[] = "Hasło może mieć maksymalnie 20 znaków.";
    if (!preg_match('/[A-Z]/', $password)) $errors[] = "Hasło musi zawierać wielką literę.";
    if (!preg_match('/[a-z]/', $password)) $errors[] = "Hasło musi zawierać małą literę.";
    if (!preg_match('/[0-9]/', $password)) $errors[] = "Hasło musi zawierać cyfrę.";
    if (!preg_match('/[^A-Za-z0-9]/', $password)) $errors[] = "Hasło musi zawierać znak specjalny.";
    return $errors;
}

$passwordErrors = [];
$emailError = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $password = $_POST['password'];
    $passwordErrors = validate_password($password);
    if (!preg_match('/^[^@\s]+@[^@\s]+\.[^@\s]+$/', $email)) {
        $emailError = 'E-mail musi być w formacie: tekst@tekst.tekst';
    }
    if (empty($passwordErrors) && empty($emailError)) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if ($stmt->fetch()) {
            $message = "Użytkownik o podanej nazwie lub e-mailu już istnieje.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $ins = $pdo->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
            if ($ins->execute([$username, $email, $hash])) {
                $message = "Rejestracja zakończona sukcesem! Możesz się zalogować.";
            } else {
                $message = "Błąd rejestracji.";
            }
        }
    }
}
include '../partials/header.php';
?>
<div class="main-content">
<div class="container">
    <form class="form-neon" method="POST">
        <h2>Rejestracja</h2>
        <?php if ($message): ?><p style="color:yellow;"><?= $message ?></p><?php endif; ?>
        <label>Nazwa użytkownika</label>
        <input type="text" name="username" placeholder="Twoja nazwa" required>
        <label>E-mail</label>
        <input type="email" name="email" placeholder="email@example.com" required>
        <div id="email-error" style="margin-top:2px;margin-bottom:8px;">
        <?php if (!empty($emailError)) {
            echo '<span class="password-error">'.$emailError.'</span>';
        } ?>
        </div>
        <label>Hasło</label>
        <input type="password" name="password" id="password" placeholder="Hasło" required oninput="validatePasswordFrontend()">
        <div id="password-errors" style="margin-top:2px;margin-bottom:8px;">
        <?php if (!empty($passwordErrors)) {
            echo '<span class="password-error">'.implode(', ', $passwordErrors).'</span>';
        } ?>
        </div>
        <button type="submit" class="button-neon">Zarejestruj</button>
        <p>Masz już konto? <a href="login.php">Zaloguj się</a></p>
    </form>
        <script>
        function validatePasswordFrontend() {
            const password = document.getElementById('password').value;
            const errors = [];
            if (password.length < 8) errors.push('Hasło musi mieć minimum 8 znaków.');
            if (password.length > 20) errors.push('Hasło może mieć maksymalnie 20 znaków.');
            if (!/[A-Z]/.test(password)) errors.push('Hasło musi zawierać wielką literę.');
            if (!/[a-z]/.test(password)) errors.push('Hasło musi zawierać małą literę.');
            if (!/[0-9]/.test(password)) errors.push('Hasło musi zawierać cyfrę.');
            if (!/[^A-Za-z0-9]/.test(password)) errors.push('Hasło musi zawierać znak specjalny.');
            const div = document.getElementById('password-errors');
            if (errors.length > 0) {
                div.innerHTML = '<span class="password-error">' + errors.join(', ') + '</span>';
            } else {
                div.innerHTML = '';
            }
        }
        </script>
    </form>
</div>
</div>
<?php include '../partials/footer.php'; ?>
