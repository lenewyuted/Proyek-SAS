<?php
require_once __DIR__ . "/../config/database.php";

function getProducts($search, $categoryId, $currentPage, $perPage)
{
    global $pdo;

    $query = "select p.*, c.name as category_name from products p 
    left join categories c on p.category_id = c.id
    where p.name like :search
    limit :limit offset :offset";

    $stmt = $pdo->prepare($query);
    $stmt->execute([
        'search' => "%$search%",
        'limit' => $perPage,
        'offset' => ($currentPage - 1) * $perPage,
    ]);

    $products = $stmt->fetchAll();

    return $products;
}

function formatRupiah($angka)
{
    // Jika angka null, kosong, atau bukan numerik, set ke 0
    $angka = is_numeric($angka) ? $angka : 0;
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

    $product = $stmt->fetch();

    if (!$product) {
        return null;
    }

    return $product;
}

function countProducts()
{
    global $pdo;
    $stmt = $pdo->query("select count(*) as total from products");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['total'] ?? 0;
}

function countOfProducts($search, $categoryId)
{
    global $pdo;

    $query = "select count(id) from products where name like :name";

    $stmt = $pdo->prepare($query);
    $stmt->execute([
        'name' => "%$search%",
    ]);

    $totalProducts = $stmt->fetchColumn();

    return $totalProducts;
}

function getLatestProducts()
{
    global $pdo;

    $query = "select * from products order by id desc limit 4";

    $stmt = $pdo->prepare($query);
    $stmt->execute([]);

    $products = $stmt->fetchAll();

    return $products;
}

function getAllProducts()
{
    global $pdo;

    $query = "SELECT p.*, c.name AS category_name
              FROM products p
              LEFT JOIN categories c ON p.category_id = c.id
              ORDER BY p.name ASC";

    $stmt = $pdo->prepare($query);
    $stmt->execute([]);

    return $stmt->fetchAll();
}

if (!function_exists('getFilteredProducts')) {
    function getFilteredProducts(PDO $pdo, string $search = '', int $categoryId = 0, int $defaultLimit = 6): array
    {
        $search     = trim($search);
        $categoryId = max(0, $categoryId);

        $queryProducts = "SELECT products.*,
                                 categories.name AS category_name,
                                 COALESCE(AVG(pr.rating), 0) AS avg_rating,
                                 COUNT(pr.id) AS total_reviews
                          FROM products
                          LEFT JOIN categories ON products.category_id = categories.id
                          LEFT JOIN `products-rating` pr ON pr.product_id = products.id";

        $conditions = [];
        $params     = [];

        if ($search !== '') {
            $likeTerm               = '%' . $search . '%';
            $conditions[]           = "(products.name LIKE :search_name OR products.description LIKE :search_desc OR categories.name LIKE :search_cat)";
            $params[':search_name'] = $likeTerm;
            $params[':search_desc'] = $likeTerm;
            $params[':search_cat']  = $likeTerm;
        }

        if ($categoryId > 0) {
            $conditions[]           = "products.category_id = :category_id";
            $params[':category_id'] = $categoryId;
        }

        if (!empty($conditions)) {
            $queryProducts .= " WHERE " . implode(" AND ", $conditions);
        }

        $queryProducts .= " GROUP BY products.id
                          ORDER BY avg_rating DESC, total_reviews DESC, products.id ASC";

        if ($search === '' && $categoryId === 0 && $defaultLimit > 0) {
            $queryProducts .= " LIMIT " . (int) $defaultLimit;
        }

        $stmtProd = $pdo->prepare($queryProducts);
        foreach ($params as $key => $val) {
            $stmtProd->bindValue($key, $val, is_int($val) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        $stmtProd->execute();

        return $stmtProd->fetchAll(PDO::FETCH_ASSOC);
    }
}
