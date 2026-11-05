<?php
require_once __DIR__ . "/../config/database.php";

// Mengambil produk (beserta nama dan icon kategorinya), bisa dicari berdasarkan judul
function getProducts($search = '', $limit = 5)
{
    global $pdo;

    $query = "select p.*, c.name as category_name, c.icon as category_icon
    from products p
    left join categories c on p.category_id = c.id
    where p.title like :search
    order by p.id asc
    limit :limit";

    $stmt = $pdo->prepare($query);
    $stmt->bindValue(':search', "%$search%");
    $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
    $stmt->execute();

    $products = $stmt->fetchAll();

    return $products;
}

// Mengambil semua produk tanpa batas, dipakai untuk halaman rekomendasi
function getAllProducts()
{
    global $pdo;

    $query = "select p.*, c.name as category_name, c.icon as category_icon
    from products p
    left join categories c on p.category_id = c.id";

    $stmt = $pdo->prepare($query);
    $stmt->execute();

    $products = $stmt->fetchAll();

    return $products;
}

function formatRupiah($angka)
{
    return 'Rp.' . number_format($angka, 0, ',', '.');
}
?>
