<?php
require_once __DIR__ . "/../repositories/product-repository.php";
require_once __DIR__ . "/../repositories/category-repository.php";

function getProductRequest($request)
{
    return [
        'id' => $request['id'] ?? null,
        'name' => trim(htmlspecialchars($request['name'])),
        'category_id' => trim(htmlspecialchars($request['category_id'])),
        'price' => trim(htmlspecialchars($request['price'])),
        'stock' => trim(htmlspecialchars($request['stock'])),
        'description' => trim(htmlspecialchars($request['description'])),
    ];
}

function validateProduct($request, $ignoreBookId = 0) // sebuah validasi akan dilewati untuk khusus buku dengan id tertentu. Ketika dikirim $ignoreBookId dengan id 1, maka buku dengan id 1 bisa dipakai untuk exclude beberapa aturan
{
    $errors = [];

    // Validate picture: required only when creating a product
    if ($ignoreBookId == 0) {
        if (!isset($_FILES['picture']) || $_FILES['picture']['error'] === 4) {
            $errors['picture'] = "Product image is required.";
        }
    }

    // Validate name : required and max 100 character
    if ($request['name'] === '') {
        $errors['name'] = "Nama produk wajib diisi";
    } elseif (mb_strlen($request['name']) > 100) { // mb_strlen untuk cek panjang karakternya
        $errors['name'] = "Nama produk tidak boleh melebihi 100 karakter";
    }

    // Validate category_id: required, isTaken?
    if ($request['category_id'] === '') {
        $errors['category_id'] = "Category_id wajib diisi";
    }

    // Validate price : required, isNumber
    if ($request['price'] === '') {
        $errors['price'] = "Harga wajib diisi";
    } elseif (!is_numeric($request['price'])) {
        $errors['price'] = "Harga wajib diisi dengan angka";
    }

    // Validate stock : required, isNumber
    if ($request['stock'] === '') {
        $errors['stock'] = "Stok wajib diisi";
    } elseif (!is_numeric($request['stock'])) {
        $errors['stock'] = "Stok wajib diisi dengan angka";
    }

    // Validate description : required and max 500 character
    if ($request['description'] === '') {
        $errors['description'] = "Deskripsi produk wajib diisi";
    } elseif (mb_strlen($request['description']) > 500) { // mb_strlen untuk cek panjang karakternya
        $errors['description'] = "Deskripsi produk tidak boleh melebihi 500 karakter";
    }

    return $errors;
}
