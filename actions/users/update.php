<?php
require_once __DIR__ . "/../../middleware/auth.php";
require_once __DIR__ . "/../../config/database.php";
require_once __DIR__ . "/../../validations/user-validation.php";

// Pastikan hanya admin yang dapat mengakses aksi ini
requireRole(["admin"]);

if (isset($_POST['update']) && $_SERVER['REQUEST_METHOD'] === "POST") {
    try {
        $pdo->beginTransaction();

        // Mengambil inputan dari pengguna menggunakan fungsi yang sudah ada
        $request = getUserRequest($_POST); 

        // Cek apakah kolom password diisi atau dibiarkan kosong
        if (!empty($request['password'])) {
            // Jika diisi, update data beserta password baru yang sudah di-hash
            $hashedPassword = password_hash($request['password'], PASSWORD_DEFAULT);

            $query = "UPDATE users SET name = :name, email = :email, password = :password, role = :role WHERE id = :id";
            $stmt = $pdo->prepare($query);
            $stmt->execute([
                'name' => $request['name'],
                'email' => $request['email'],
                'password' => $hashedPassword,
                'role' => $request['role'],
                'id' => $request['id'],
            ]);
        } else {
            // Jika kosong, update data lainnya TANPA mengubah/menimpa password lama
            $query = "UPDATE users SET name = :name, email = :email, role = :role WHERE id = :id";
            $stmt = $pdo->prepare($query);
            $stmt->execute([
                'name' => $request['name'],
                'email' => $request['email'],
                'role' => $request['role'],
                'id' => $request['id'],
            ]);
        }

        $pdo->commit();

        header('location: ../../pages/admin/users/index.php');
        exit;

    } catch (PDOException $e) {
        $pdo->rollBack();
        throw $e;
    }
}
?>