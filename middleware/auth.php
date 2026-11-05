<?php
// Gerbang sebelum masuk ke sebuah halaman atau action.
// Halaman login belum dibuat, jadi untuk sementara admin yang aktif adalah user dengan id 1.
// Ketika login sudah ada, cukup simpan $_SESSION['user_id'] saat berhasil login.

const DEFAULT_ADMIN_ID = 1;

// Mengecek apakah sebuah session sudah dimulai atau belum, jika belum maka session akan dimulai
function sessionStart()
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

// Mendapatkan id pengguna yang sedang login
function getAuthUserId()
{
    sessionStart(); // membuka session

    return $_SESSION['user_id'] ?? DEFAULT_ADMIN_ID; // kalau belum ada, pakai admin default
}
?>
