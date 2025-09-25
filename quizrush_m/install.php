<?php
// QuizRush - Instalator
error_reporting(E_ALL);
ini_set('display_errors', 1);

function showForm($error = '') {
    echo '<h2>Instalacja QuizRush</h2>';
    if ($error) echo '<p style="color:red;">'.$error.'</p>';
    echo '<form method="post">
        <label>Host bazy danych: <input type="text" name="dbhost" value="localhost" required></label><br>
        <label>Nazwa bazy danych: <input type="text" name="dbname" required></label><br>
        <label>Użytkownik bazy: <input type="text" name="dbuser" required></label><br>
        <label>Hasło bazy: <input type="password" name="dbpass"></label><br>
        <button type="submit">Instaluj</button>
    </form>';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dbhost = $_POST['dbhost'];
    $dbname = $_POST['dbname'];
    $dbuser = $_POST['dbuser'];
    $dbpass = $_POST['dbpass'];

    // Połączenie z bazą
    $conn = @mysqli_connect($dbhost, $dbuser, $dbpass);
    if (!$conn) {
        showForm('Błąd połączenia z bazą danych: '.mysqli_connect_error());
        exit;
    }
    // Utwórz bazę jeśli nie istnieje
    mysqli_query($conn, "CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
    mysqli_select_db($conn, $dbname);

    // Wykonaj plik SQL
    $sqlFile = __DIR__ . '/' . $dbname . '.sql';
    if (!file_exists($sqlFile)) {
        // Fallback do quizrush.sql
        $sqlFile = __DIR__ . '/quizrush.sql';
        if (!file_exists($sqlFile)) {
            echo '<p style="color:red;">Brak pliku ' . htmlspecialchars($dbname) . '.sql oraz quizrush.sql!</p>';
            exit;
        } else {
            echo '<p style="color:orange;">Nie znaleziono pliku ' . htmlspecialchars($dbname) . '.sql, używam quizrush.sql.</p>';
        }
    }
    $sql = file_get_contents($sqlFile);
    $queries = array_filter(array_map('trim', explode(';', $sql)));
    $ok = true;
    foreach ($queries as $q) {
        if ($q) {
            if (!mysqli_query($conn, $q)) {
                echo '<p style="color:red;">Błąd SQL: '.mysqli_error($conn).'</p>';
                $ok = false;
            }
        }
    }
    if ($ok) {
        // Dodaj domyślnego admina
        $adminUser = 'admin';
        $adminEmail = 'admin@quizrush.pl';
        $adminPass = password_hash('admin123', PASSWORD_DEFAULT);
        $adminRole = 'admin';
        $adminActive = 1;
        $adminNickname = 'Administrator';
        $checkAdmin = mysqli_query($conn, "SELECT id FROM users WHERE username='$adminUser' OR email='$adminEmail'");
        if (mysqli_num_rows($checkAdmin) == 0) {
            $result = mysqli_query($conn, "INSERT INTO users (username, email, password_hash, role, is_active, nickname) VALUES ('$adminUser', '$adminEmail', '$adminPass', '$adminRole', $adminActive, '$adminNickname')");
            if ($result) {
                echo '<p style="color:lime;">Domyślny admin utworzony:<br>Email: <b>email@email.pl</b><br>Hasło: <b>admin</b></p>';
            } else {
                echo '<p style="color:red;">Wystąpił błąd podczas tworzenia admina: ' . mysqli_error($conn) . '</p>';
            }
        } else {
            echo '<p style="color:orange;">Domyślny admin utworzony:<br>Email: <b>email@email.pl</b><br>Hasło: <b>admin</b></p>';
        }
        // Zapisz config/db.php
        $safeDbhost = addslashes($dbhost);
        $safeDbname = addslashes($dbname);
        $safeDbuser = addslashes($dbuser);
        $safeDbpass = addslashes($dbpass);
        $config = "<?php\nreturn new PDO('mysql:host=$safeDbhost;dbname=$safeDbname;charset=utf8mb4', '$safeDbuser', '$safeDbpass');\n";
    file_put_contents(__DIR__.'/../config/db.php', $config);
        echo '<h3>Instalacja zakończona sukcesem!</h3>';
        echo '<p>Możesz usunąć plik install.php ze względów bezpieczeństwa.</p>';
        echo '<a href="/quizrush/index.php">Przejdź do strony głównej</a>';
    } else {
        echo '<p style="color:red;">Wystąpiły błędy podczas instalacji. Sprawdź plik SQL i dane bazy.</p>';
    }
    exit;
}

showForm();
