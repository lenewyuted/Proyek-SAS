<?php
require_once __DIR__ . "/../repositories/product-repository.php";
require_once __DIR__ . "/../repositories/category-repository.php";

function getProductRequest($request) 
{
    return [
        'name' => trim(htmlspecialchars($request['name'])),
        'category_id' => trim(htmlspecialchars($request['category_id'])),
        'price' => trim(htmlspecialchars($request['price'])),
        'stock' => trim(htmlspecialchars($request['stock'])),
        'description' => trim(htmlspecialchars($request['description'])),
    ];
}

function validateProduct($request, $ignoreBookId = 0) //sebuah validasi akan dilewati untuk khusus buku dengan id tertentu. Ketika dikirim $ignoreBookId dgn id 1, maka buku dengan id 1 bs dipakai untuk exclude beberapa aturan  
{
    $errors = [];

    //Validate name : required and max 100 character
    if ($request['name'] === '') {
        $errors['name'] = "Judul buku wajib diisi";
    }
    elseif (mb_strlen($request['name']) > 100) { //mb_strlen untuk cek panjang karakternya
        $errors['name'] = "Judul buku tidak boleh melebihi 100 karakter";
    }

    //Validate category_id: required, isTaken?
    if($request['category_id'] === '') {
        $errors['category_id'] = "category_id wajib diisi";
    }

    //Validate price : required, isNumber
    if($request['price'] === '') {
        $errors['price'] = "Tahun terbit wajib diisi";
    }
    elseif(!is_int(intval($request['price']))) {
        $errors['price'] = "Tahun terbit wajib diisi dengan angka";
    }


    //Validate stock : required, isNumber
    if($request['stock'] === '') {
        $errors['stock'] = "Stok wajib diisi";
    }
    elseif(!is_int(intval($request['stock']))) {
        $errors['stock'] = "Stok wajib diisi dengan angka";
    }



    //Validate description : required and max 200 character
    if ($request['description'] === '') {
        $errors['description'] = "Deskripsi buku wajib diisi";
    }
    elseif (mb_strlen($request['description']) > 100) { //mb_strlen untuk cek panjang karakternya
        $errors['description'] = "Deskripsi buku tidak boleh melebihi 200 karakter";
    }

    return $errors;
}
?>