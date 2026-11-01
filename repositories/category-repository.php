<?php
require_once __DIR__ . "/../config/database.php";

function getCategories() 
{
    global $pdo;

    $query = "select c.*, count(p.id) as total_products
    from categories c
    left join products p on p.category_id = c.id
    group by c.id";

    $stmt = $pdo ->prepare($query);
    $stmt -> execute();

    $categories = $stmt -> fetchAll();

    return $categories;
}


function getCategory()
{
    $category = [
        "id" => 1,
        "name" => "CPU",
        "description" => "The core brain of the computer that processes data and executes instructions",
    ];

    return $category;
}

function isCategoryExists($categoryId) 
{
  global $pdo;

  $query = "select count(id) from categories where id = :id"; //count(id) menghitung jumlah data yg ada di tabel book

  $stmt = $pdo -> prepare($query);
  $stmt -> execute([
    'id' => $categoryId   // sebelumnya 'category_id', harus sama dengan :id di query
  ]);

  $isCategoryExists = $stmt -> fetchColumn() > 0;

  return $isCategoryExists;
}
?>