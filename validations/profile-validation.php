<?php
require_once __DIR__ . "/../repositories/user-repository.php";

function getProfileRequest($request)
{
    return [
        'full_name' => trim($request['full_name'] ?? ''),
        'username' => trim($request['username'] ?? ''),
        'email' => trim($request['email'] ?? ''),
        'phone' => trim($request['phone'] ?? ''),
        'address' => trim($request['address'] ?? ''),
        'bio' => trim($request['bio'] ?? ''),
    ];
}

function validateProfile($request, $ignoreUserId = 0) // pengguna dengan id tertentu dikecualikan saat cek username dan email kembar
{
    $errors = [];

    // Validate full_name : required and max 100 character
    if ($request['full_name'] === '') {
        $errors['full_name'] = "Nama lengkap wajib diisi";
    } elseif (mb_strlen($request['full_name']) > 100) { // mb_strlen untuk cek panjang karakternya
        $errors['full_name'] = "Nama lengkap tidak boleh melebihi 100 karakter";
    }

    // Validate username : required, max 50 character, and unique
    if ($request['username'] === '') {
        $errors['username'] = "Username wajib diisi";
    } elseif (mb_strlen($request['username']) > 50) {
        $errors['username'] = "Username tidak boleh melebihi 50 karakter";
    } elseif (isUsernameTaken($request['username'], $ignoreUserId)) {
        $errors['username'] = "Username sudah digunakan";
    }

    // Validate email : required, valid format, max 100 character, and unique
    if ($request['email'] === '') {
        $errors['email'] = "Email wajib diisi";
    } elseif (!filter_var($request['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Format email tidak valid";
    } elseif (mb_strlen($request['email']) > 100) {
        $errors['email'] = "Email tidak boleh melebihi 100 karakter";
    } elseif (isEmailTaken($request['email'], $ignoreUserId)) {
        $errors['email'] = "Email sudah digunakan";
    }

    // Validate phone : optional, but must be a valid phone number
    if ($request['phone'] !== '' && !preg_match('/^[0-9+\-\s]{8,20}$/', $request['phone'])) {
        $errors['phone'] = "Nomor telepon tidak valid";
    }

    // Validate address : optional, max 200 character
    if (mb_strlen($request['address']) > 200) {
        $errors['address'] = "Alamat tidak boleh melebihi 200 karakter";
    }

    // Validate photo : optional, but must be jpg/jpeg/png/webp and max 2 MB
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] !== UPLOAD_ERR_NO_FILE) {
        $extension = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));

        if ($_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
            $errors['photo'] = "Foto gagal diunggah";
        } elseif (!in_array($extension, ['jpg', 'jpeg', 'png', 'webp'], true)) {
            $errors['photo'] = "Foto harus berformat JPG, PNG, atau WEBP";
        } elseif ($_FILES['photo']['size'] > 2 * 1024 * 1024) {
            $errors['photo'] = "Ukuran foto maksimal 2 MB";
        }
    }

    return $errors;
}
?>
