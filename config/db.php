<?php
$host = 'localhost';
$dbname = 'gender_identity';
$username = 'root'; // Username
$password = ''; // Parol

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Ulanishda xatolik: " . $e->getMessage());
}
?>