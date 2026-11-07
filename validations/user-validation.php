<?php
require_once __DIR__ . "/../repositories/user-repository.php";

function getUserRequest($request)
{
    return [
        'id' => $request['id'] ?? null,
        'name' => trim(htmlspecialchars($request['name'])),
        'email' => trim(htmlspecialchars($request['email'])),
        'password' => trim(htmlspecialchars($request['password'])),
        'role' => trim(htmlspecialchars($request['role'])),
    ];
}

function validateUser($request, $ignoreUserId = 0) // sebuah validasi akan dilewati untuk khusus buku dengan id tertentu. Ketika dikirim $ignoreUserId dengan id 1, maka buku dengan id 1 bisa dipakai untuk exclude beberapa aturan
{
    $errors = [];

    // Validate name : required and max 100 character
    if ($request['name'] === '') {
        $errors['name'] = "Nama wajib diisi";
    } elseif (mb_strlen($request['name']) > 100) { // mb_strlen untuk cek panjang karakternya
        $errors['name'] = "Nama tidak boleh melebihi 100 karakter";
    }

    // Validate email : required, valid format, and must be @gmail.com
    if ($request['email'] === '') {
        $errors['email'] = "Email wajib diisi";
    } elseif (!filter_var($request['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Format email tidak valid";
    } elseif (!str_ends_with(strtolower($request['email']), '@gmail.com')) {
        $errors['email'] = "Email harus menggunakan domain @gmail.com";
    } elseif (mb_strlen($request['email']) > 100) { 
        $errors['email'] = "Email tidak boleh melebihi 100 karakter";
    }

    // Validate password : required, isNumber
    if ($request['password'] === '') {
        $errors['password'] = "Password wajib diisi";
    } elseif (mb_strlen($request['name']) > 100) { // mb_strlen untuk cek panjang karakternya
        $errors['password'] = "Password tidak boleh melebihi 100 karakter";
    }

    // Validate role : required and max 500 character
    if ($request['role'] === '') {
        $errors['role'] = "Role wajib dipilih";
    } 

    return $errors;
}
