<?php
$host = "localhost"; 
$dbname = "Proyek_SAS";
$username = "root";
$password = "";

$dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,  
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, 
    PDO::ATTR_EMULATE_PREPARES => false, 
];

try {
    $pdo = new PDO($dsn, $username, $password, $options); //ini namanya constructor. variabel ini akan kita pakai untuk jika kita ingin berinteraksi dengan database kita
} catch (PDOException $error) { 
    die("Koneksi ke database gagal: " . $error -> getMessage()); //Jika terjadi error, maka aplikasinya akan langsung dihentikan menggunakan die dan memunculkan pesan dan alasan kenapa gagal koneksi
}
?>
