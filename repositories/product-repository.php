<?php 
require_once __DIR__ . "/../config/database.php";

function getProducts()
{
    global $pdo;

    $query = "select p.*, c.name as category_name from products p 
    left join categories c on p.category_id = c.id" ;

    $stmt = $pdo -> prepare($query);
    $stmt -> execute([]);

    $products = $stmt -> fetchAll();

    return $products;
}

function formatRupiah($angka) {
    return 'Rp ' . number_format($angka, 0, ',', '.');
}


function getProduct($id)
{
    global $pdo;

    $query = "SELECT * FROM products WHERE id = :id";

    $stmt = $pdo->prepare($query);
    $stmt->execute([
        'id' => $id
    ]);

    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        header('location: ../pages/admin/products/index.php');
        exit;
    }

    return $product;
}
?>

