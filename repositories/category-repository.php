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


function getCategory($id)
{
    global $pdo;

    $query = "select * from categories where id = :id";

    $stmt = $pdo->prepare($query);
    $stmt->execute([
        'id' => $id
    ]);

    $category = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$category) {
        header('location: ../pages/admin/categories/index.php');
        exit;
    }

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

function countCategories() {
    global $pdo; 
    $stmt = $pdo->query("select count(*) as total from categories");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['total'] ?? 0;
}


?>