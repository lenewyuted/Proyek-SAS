<?php
require_once __DIR__ . "/../config/database.php";

function getCategories($limit = 8)
{
    global $pdo;

    $query = "select * from categories order by id asc limit :limit";

    $stmt = $pdo->prepare($query);
    $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT); // limit harus berupa angka, jadi di-bind sebagai integer
    $stmt->execute();

    $categories = $stmt->fetchAll();

    return $categories;
}
?>
