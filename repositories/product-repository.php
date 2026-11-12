<?php 
require_once __DIR__ . "/../config/database.php";

function getProducts($search, $categoryId, $currentPage, $perPage)
{
    global $pdo;

    $query = "select p.*, c.name as category_name from products p 
    left join categories c on p.category_id = c.id
    where p.name like :search
    limit :limit offset :offset" ;

    $stmt = $pdo -> prepare($query);
    $stmt -> execute([
        'search' => "%$search%",
        'limit' => $perPage,
        'offset' => ($currentPage -1) * $perPage,
    ]);

    $products = $stmt -> fetchAll();

    return $products;
}

function formatRupiah($angka) {
    return 'Rp ' . number_format($angka, 0, ',', '.');
}


function getProduct($id)
{
    global $pdo;

    $query = "select * from products where id = :id";

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

function countProducts() {
    global $pdo; 
    $stmt = $pdo->query("select count(*) as total from products");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['total'] ?? 0;
}


function countOfProducts($search, $categoryId) 
{
    global $pdo;

    $query = "select count(id) from products where name like :name";

    $stmt = $pdo -> prepare($query);
    $stmt->execute([
        'name' => "%$search%",
    ]);

    $totalProducts = $stmt -> fetchColumn();

    return $totalProducts;
}

function getLatestProducts()
{
    global $pdo;

    $query = "select * from products order by id desc limit 3";

    $stmt = $pdo -> prepare($query);
    $stmt -> execute([]);

    $products = $stmt -> fetchAll();

    return $products;
}
?>

