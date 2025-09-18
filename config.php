<?php
$DB_HOST = 'localhost';
$DB_NAME = 'shopdb';
$DB_USER = 'root';
$DB_PASS = ''; // XAMPP ค่าเริ่มต้นคือว่าง

try {
  $pdo = new PDO("mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4", $DB_USER, $DB_PASS, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  ]);
} catch (PDOException $e) {
  die('Database connection failed: ' . $e->getMessage());
}

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
