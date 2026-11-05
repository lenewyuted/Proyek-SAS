<?php
//gerbang pintu utama sebelum masuk ke sebuah halaman
//mekanisme untuk menghandle sesuatu kepada pengguna yg dmn sebelum dia akan mengakses sebuah halaman atau action.
//sebelum masuk ke halaman daftar buku, kt bs cek dlu status user dan tangkap aksi yg dilakukan

const LOGIN_URL = "/pages/auth/login.php";//simpan lokasi halaman login
const LANDING_PAGE =  "/pages/landing-page/index.php";//menyimpan lokasi halaman daftar buku
const ADMIN_DASHBOARD = "/pages/admin/dashboard/index.php";
const FORBIDDEN_URL = "/pages/errors/403.php"; //memberitahu tindakan yg dilarang

//Mengecek apakah sebuah session itu sudah dimulai atau belum, jika belum maka session akan dimulai
function sessionStart()
{
    if(session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

//Mengecek apakah seorang pengguna sudah login atau belum
function isLoggedIn()
{
    sessionStart(); //membuka session
    $isLoggedIn = isset($_SESSION['user']); //mengecek apakah session tersebut sudah dideklarasikan
    //session tersebut sudah dideklarasikan ketika user sudah login 
    //\actions\auth\login.php

    return $isLoggedIn; //mengembalikan boolean
}


//Mendapatkan data pengguna yang sudah login 
function getAuthUser()
{
    sessionStart(); //membuka session
    $user = $_SESSION['user'] ?? ''; //dapatin data pengguna dari mana
    //mendapatkan data pengguna dari $_SESSION['user'], kalau tidak ada kembalikan string kosong

    return $user;
}


//Mengecek apakah role pengguna itu sesuai atau tidak
function hasRole(array $roles)
{
    $user = getAuthUser();
    $isUserHasAuthorizeRole = $user != null && in_array($user['role'], $roles); //in_array parameter pertama itu apa yg ingin km cari, contohnya string admin. parameter kedua itu array tergantung pada apa yang ingin kita cari,  contoh parameter pertama = "admin", parameter kedua isinya ["admin", "member"]. in_array melakukan pengecekan apakah parameter kedua memiliki setidaknya 1 data pada parameter pertama 
    
    return $isUserHasAuthorizeRole;
}

//Menghandle pengguna yg belum login
function requireAuth()
{
    if(!isLoggedIn()) {
        $_SESSION['errors'] = [
            'login' => "Silakan login terlebih dahulu sebelum mengakses halaman ini"
        ];

        header('location: ' . LOGIN_URL);
        exit;
    }
}

//Menghandle pengguna yg tidak memiliki role yg sesuai atau tidak terautorisasi
function requireRole(array $roles) 
{
    requireAuth();
    
    if(!hasRole($roles)) {
        header('location: ' . FORBIDDEN_URL);
        exit;
    }
}

//Menghandle pengguna yg sudah login sebelumnya
function requireGuest()
{
    if(isLoggedIn()) {
        header('location: ' . LANDING_PAGE);
        exit; //menonaktifkan kode setelahnya 
    }
}
?>
