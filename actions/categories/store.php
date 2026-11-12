<?php
session_start();
require_once __DIR__ . "/../../config/database.php";
require_once __DIR__ . "/../../validations/category-validation.php";

if (isset($_POST['store']) && $_SERVER['REQUEST_METHOD'] === "POST") {
    try {
        $pdo->beginTransaction();

        $request = getCategoryRequest($_POST);
        $errors = validateCategory($request);

        if (count($errors) > 0) {
            $_SESSION['errors'] = $errors; //simpan pesan error ke session (tempat penyimpanan sementara yg berada di server)
            $_SESSION['old'] = $request; //biasa ketika ada validasi error, kebiasaan form akan mereset semua inputannya. Jdi dgn ini bs mempertahankan semua inputannya

            header('location: ../../pages/admin/categories/create.php');
            exit;
        }

        $query = "insert into categories (name, description, picture) 
                 values (:name, :description, :picture)";

        // LOGIKA UPLOAD FILE
        $picturePath = null;

        if (isset($_FILES['picture']) && $_FILES['picture']['error'] === 0) {

            // 1. Ambil nama asli file
            $originalName = $_FILES['picture']['name'];

            // 2. Normalisasi: Ganti semua spasi dengan underscore (_)
            $safeName = str_replace(' ', '_', $originalName);

            // 3. Lokasi folder penyimpanan
            $uploadDir = __DIR__ . "/../../assets/img/category/";

            // 4. Tentukan lokasi folder dan gabungkan dengan nama yang sudah aman
            $destination = $uploadDir . $safeName;

            if (move_uploaded_file($_FILES['picture']['tmp_name'], $destination)) {
                $picturePath = $safeName;
            }
        }

        $stmt = $pdo->prepare($query);
        $stmt->execute([
            'name' => $request['name'],
            'description' => $request['description'],
            'picture' => $picturePath,
        ]);

        $pdo->commit(); //ketika sudah slesai, kt akan memastikan bahwa data ini udah bnr jdi ini akan dieksekusi dan dikirim ke databasenya

        header('location: ../../pages/admin/categories/index.php');
        exit;
    } catch (\PDOException $err) {
        $pdo->rollBack();
        throw $err;
    }
}
