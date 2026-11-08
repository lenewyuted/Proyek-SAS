<?php
require_once __DIR__ . "/../repositories/user-repository.php";

function getProfileRequest($request)
{
    return [
        'id' => $request['id'] ?? null,
        'name' => trim(htmlspecialchars($request['name'])),
        'role' => trim(htmlspecialchars($request['role'])),
        'email' => trim(htmlspecialchars($request['email'])),
        'phone_number' => trim(htmlspecialchars($request['phone_number'])),
        'address' => trim(htmlspecialchars($request['address'])),
        'bio' => trim(htmlspecialchars($request['bio'])),
    ];
}

function validateProfile($request, $ignoreProfileId = 0) // sebuah validasi akan dilewati untuk khusus buku dengan id tertentu. Ketika dikirim $ignoreUserId dengan id 1, maka buku dengan id 1 bisa dipakai untuk exclude beberapa aturan
{
    $errors = [];

    // Validate name : required and max 100 character
    if ($request['name'] === '') {
        $errors['name'] = "Nama wajib diisi";
    } elseif (mb_strlen($request['name']) > 100) { // mb_strlen untuk cek panjang karakternya
        $errors['name'] = "Nama tidak boleh melebihi 100 karakter";
    }


    // Validate role : required and max 500 character
    if ($request['role'] === '') {
        $errors['role'] = "Role wajib dipilih";
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

    // Validate phone_number : required, numeric, and exactly 12 digits
    if ($request['phone_number'] === '') {
        $errors['phone_number'] = "Nomor telepon wajib diisi";
    } elseif (!is_numeric($request['phone_number'])) {
        $errors['phone_number'] = "Nomor telepon wajib diisi dengan angka";
    } elseif (strlen($request['phone_number']) !== 12) {
        $errors['phone_number'] = "Nomor telepon harus tepat 12 digit";
    }

    // Validate password : required, isNumber
    if ($request['address'] === '') {
        $errors['address'] = "Alamat wajib diisi";
    } elseif (mb_strlen($request['address']) > 500) { // mb_strlen untuk cek panjang karakternya
        $errors['address'] = "Alamat tidak boleh melebihi 500 karakter";
    }

    // Validate bio : required and max 500 character
    if ($request['bio'] === '') {
        $errors['bio'] = "Bio wajib diisi";
    } elseif (mb_strlen($request['bio']) > 500) { // mb_strlen untuk cek panjang karakternya
        $errors['bio'] = "Bio tidak boleh melebihi 500 karakter";
    }

    return $errors;
}
