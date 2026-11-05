<?php
$host = "localhost";
$dbname = "db_techhub";
$username = "root";
$password = "";
$charset = "utf8mb4";

$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $username, $password, $options); // variabel ini dipakai setiap kali kita ingin berinteraksi dengan database
} catch (PDOException $error) {
    die("Koneksi ke database gagal: " . $error->getMessage()); // aplikasi dihentikan dan memunculkan alasan kenapa gagal koneksi
}
?>
