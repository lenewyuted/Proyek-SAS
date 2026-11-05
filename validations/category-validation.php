<?php
require_once __DIR__ . "/../repositories/product-repository.php";
require_once __DIR__ . "/../repositories/category-repository.php";

function getCategoryRequest($request)
{
    return [
        'id' => $request['id'] ?? null,
        'name' => trim(htmlspecialchars($request['name'])),
        'description' => trim(htmlspecialchars($request['description'])),
    ];
}

function validateCategory($request, $ignoreCategoryId = 0) // sebuah validasi akan dilewati untuk khusus buku dengan id tertentu. Ketika dikirim $ignoreBookId dengan id 1, maka buku dengan id 1 bisa dipakai untuk exclude beberapa aturan
{
    $errors = [];

    // Validate picture: required only when creating a product
    if ($ignoreCategoryId == 0) {
        if (!isset($_FILES['picture']) || $_FILES['picture']['error'] === 4) {
            $errors['picture'] = "Category image is required.";
        }
    }

    // Validate name : required and max 100 character
    if ($request['name'] === '') {
        $errors['name'] = "Nama category wajib diisi";
    } elseif (mb_strlen($request['name']) > 100) { // mb_strlen untuk cek panjang karakternya
        $errors['name'] = "Nama category tidak boleh melebihi 100 karakter";
    }

    // Validate description : required and max 500 character
    if ($request['description'] === '') {
        $errors['description'] = "Deskripsi category wajib diisi";
    } elseif (mb_strlen($request['description']) > 500) { // mb_strlen untuk cek panjang karakternya
        $errors['description'] = "Deskripsi category tidak boleh melebihi 500 karakter";
    }

    return $errors;
}
