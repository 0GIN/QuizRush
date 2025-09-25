<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$pdo = require_once __DIR__.'/../config/db.php';
?>