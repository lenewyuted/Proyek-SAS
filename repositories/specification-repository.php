<?php
require_once __DIR__ . "/../config/database.php";

/**
 * Mengambil daftar nama spesifikasi unik (spec_name) yang dimiliki oleh kategori tertentu.
 */
function getCategorySpecificationKeys($categoryId)
{
    global $pdo;

    $query = "SELECT ps.spec_name 
              FROM product_specifications ps
              JOIN products p ON ps.product_id = p.id
              WHERE p.category_id = :category_id
              GROUP BY ps.spec_name
              ORDER BY MIN(ps.id) ASC";

    $stmt = $pdo->prepare($query);
    $stmt->execute(['category_id' => $categoryId]);
    $result = $stmt->fetchAll(PDO::FETCH_COLUMN);

    return $result;
}

/**
 * Mengambil semua spesifikasi milik satu produk tertentu.
 */
function getProductSpecifications($productId)
{
    global $pdo;

    $query = "SELECT spec_name, spec_value 
              FROM product_specifications 
              WHERE product_id = :product_id 
              ORDER BY id ASC";

    $stmt = $pdo->prepare($query);
    $stmt->execute(['product_id' => $productId]);

    return $stmt->fetchAll();
}

/**
 * Mengambil seluruh produk berdasarkan category_id.
 */
function getProductsByCategoryId($categoryId)
{
    global $pdo;

    $query = "SELECT p.*, c.name AS category_name
              FROM products p
              LEFT JOIN categories c ON p.category_id = c.id
              WHERE p.category_id = :category_id
              ORDER BY p.name ASC";

    $stmt = $pdo->prepare($query);
    $stmt->execute(['category_id' => $categoryId]);

    return $stmt->fetchAll();
}

/**
 * Mengambil seluruh spesifikasi produk dalam kategori, 
 * dikelompokkan ke array asosiatif: [product_id => [spec_name => spec_value]]
 */
function getProductSpecsMapByCategoryId($categoryId)
{
    global $pdo;

    $query = "SELECT ps.product_id, ps.spec_name, ps.spec_value
              FROM product_specifications ps
              JOIN products p ON ps.product_id = p.id
              WHERE p.category_id = :category_id
              ORDER BY ps.id ASC";

    $stmt = $pdo->prepare($query);
    $stmt->execute(['category_id' => $categoryId]);
    $rows = $stmt->fetchAll();

    $specsMap = [];
    foreach ($rows as $row) {
        $productId = (int) $row['product_id'];
        $specName = $row['spec_name'];
        $specValue  = $row['spec_value'];
        $specsMap[$productId][$specName] = $specValue;
    }

    return $specsMap;
}

/**
 * Menyinkronkan (simpan ulang) spesifikasi produk.
 * Pola: Hapus spesifikasi lama milik produk, lalu insert spesifikasi baru yang diisi.
 */
function syncProductSpecifications($productId, array $specNames, array $specValues)
{
    global $pdo;

    // 1. Hapus spesifikasi lama milik produk
    $deleteQuery = "DELETE FROM product_specifications WHERE product_id = :product_id";
    $deleteStmt = $pdo->prepare($deleteQuery);
    $deleteStmt->execute(['product_id' => $productId]);

    // 2. Insert spesifikasi baru jika ada
    if (!empty($specNames)) {
        $insertQuery = "INSERT INTO product_specifications (product_id, spec_name, spec_value) 
                        VALUES (:product_id, :spec_name, :spec_value)";
        $insertStmt = $pdo->prepare($insertQuery);

        foreach ($specNames as $index => $specName) {
            $specValue = $specValues[$index] ?? '';
            $trimmedName = trim($specName);
            $trimmedValue = trim($specValue);

            // Hanya simpan jika nama dan nilai tidak kosong
            if ($trimmedName !== '' && $trimmedValue !== '') {
                $insertStmt->execute([
                    'product_id' => $productId,
                    'spec_name' => $trimmedName,
                    'spec_value' => $trimmedValue,
                ]);
            }
        }
    }
}
