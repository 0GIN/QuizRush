<?php
require_once 'config/db.php';

$stmt = $pdo->query("SELECT COUNT(*) AS total_users FROM users");
$row = $stmt->fetch();

echo "Połączenie działa! Liczba użytkowników: " . $row['total_users'];
?>
