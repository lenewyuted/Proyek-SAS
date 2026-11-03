<?php
require_once __DIR__ . "/../../middleware/auth.php";
require_once __DIR__ . "/../../config/database.php";
require_once __DIR__ . "/../../validations/product-validation.php";

if (isset($_POST['update']) && $_SERVER['REQUEST_METHOD'] === "POST") {
    try {
        $pdo->beginTransaction();

        // ketika panggil function ini, function ini akan menerima inputan dari pengguna
        $request = getProductRequest($_POST); // berisi inputan dari pengguna

        $errors = validateProduct($request, $request['id']); // untuk menampung pesan terkait dengan inputan yang mau kita validasi

        if (count($errors) > 0) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $request;

            header('location: ../../pages/admin/products/edit.php?id=' . urlencode($request['id']));
            exit;
        }

        // Ambil gambar lama dari database
        $query = "select picture from products where id = :id";
        $stmt = $pdo->prepare($query);
        $stmt->execute([
            'id' => $request['id']
            ]);
        $picturePath = $stmt->fetchColumn();

        // LOGIKA UPLOAD GAMBAR BARU
        if (isset($_FILES['picture']) && $_FILES['picture']['error'] === 0) {

            // Ambil nama asli file
            $originalName = $_FILES['picture']['name'];

            // Ganti spasi dengan underscore
            $safeName = str_replace(' ', '_', $originalName);

            // Tentukan lokasi folder penyimpanan
            $uploadDir = __DIR__ . "/../../assets/img/";

            // Gabungkan lokasi folder dengan nama file
            $destination = $uploadDir . $safeName;

            if (move_uploaded_file($_FILES['picture']['tmp_name'], $destination)) {
                $picturePath = $safeName;
            }
        }

        // Update main data
        $query = "update products set name = :name, description = :description, price = :price, stock = :stock, picture = :picture, category_id = :category_id where id = :id";

        $stmt = $pdo->prepare($query);
        $stmt->execute([
            'name' => $request['name'],
            'description' => $request['description'],
            'price' => $request['price'],
            'stock' => $request['stock'],
            'picture' => $picturePath,
            'category_id' => $request['category_id'],
            'id' => $request['id'],
        ]);

        $pdo->commit();

        header('location: ../../pages/admin/products/index.php');
        exit;

    } catch (PDOException $e) {
        $pdo->rollBack();
        throw $e;
    }
}
?>
