<?php
require_once __DIR__ . "/../repositories/product-repository.php";
require_once __DIR__ . "/../repositories/category-repository.php";

function getProductRequest($request)
{
    return [
        'id'          => $request['id'] ?? null,
        'name'        => trim(htmlspecialchars((string) ($request['name'] ?? ''), ENT_QUOTES, 'UTF-8')),
        'category_id' => trim(htmlspecialchars((string) ($request['category_id'] ?? ''), ENT_QUOTES, 'UTF-8')),
        'price'       => trim(htmlspecialchars((string) ($request['price'] ?? ''), ENT_QUOTES, 'UTF-8')),
        'stock'       => trim(htmlspecialchars((string) ($request['stock'] ?? ''), ENT_QUOTES, 'UTF-8')),
        'description' => trim((string) ($request['description'] ?? '')),
    ];
}

function validateProduct($request, $ignoreBookId = 0)
{
    $errors = [];

    if ($ignoreBookId == 0) {
        if (!isset($_FILES['picture']) || $_FILES['picture']['error'] === 4) {
            $errors['picture'] = "Product image is required.";
        }
    }

    if ($request['name'] === '') {
        $errors['name'] = "Nama produk wajib diisi";
    } elseif (mb_strlen($request['name']) > 100) {
        $errors['name'] = "Nama produk tidak boleh melebihi 100 karakter";
    }

    if ($request['category_id'] === '') {
        $errors['category_id'] = "Category wajib dipilih";
    }

    if ($request['price'] === '') {
        $errors['price'] = "Harga wajib diisi";
    } elseif (!is_numeric($request['price'])) {
        $errors['price'] = "Harga wajib diisi dengan angka";
    }

    if ($request['stock'] === '') {
        $errors['stock'] = "Stok wajib diisi";
    } elseif (!is_numeric($request['stock'])) {
        $errors['stock'] = "Stok wajib diisi dengan angka";
    }

    if ($request['description'] === '') {
        $errors['description'] = "Deskripsi produk wajib diisi";
    } elseif (mb_strlen($request['description']) > 500) {
        $errors['description'] = "Deskripsi produk tidak boleh melebihi 500 karakter";
    }

    return $errors;
}
