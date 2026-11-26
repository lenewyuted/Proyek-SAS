<?php
session_start();
require_once __DIR__ . "/../../middleware/auth.php";
require_once __DIR__ . "/../../config/database.php";
require_once __DIR__ . "/../../validations/profile-validation.php";

requireRole(["admin"]);

if (isset($_POST['update']) && $_SERVER['REQUEST_METHOD'] === "POST") {
    try {
        $pdo->beginTransaction();

        // Ambil ID user yang sedang login dari session secara aman
        $userId = $_SESSION['user_id'] ?? $_SESSION['user']['id'] ?? $_SESSION['id'] ?? null;
        if (!$userId) {
            throw new Exception("Unauthorized user ID.");
        }

        // Tambahkan ID ke dalam data POST agar terbaca oleh fungsi validasi
        $_POST['id'] = $userId;

        // Mengambil inputan dari pengguna menggunakan fungsi validasi profil
        $request = getProfileRequest($_POST); 

        // Validasi inputan profil
        $errors = validateProfile($request, $request['id']);
    
        if (count($errors) > 0) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $request;

            header('location: ../../pages/admin/profile/edit.php');
            exit;
        }

        // Susun Query Update Database (Fokus pada data teks profile tanpa mengubah foto & email/role yang dikunci)
        $query = "update users set name = :name, phone_number = :phone_number, address = :address, bio = :bio where id = :id";
        $stmt = $pdo->prepare($query);
        $stmt->execute([
            'name'    => $request['name'],
            'phone_number' => $request['phone_number'] ?? '',
            'address' => $request['address'] ?? '',
            'bio'     => $request['bio'] ?? '',
            'id'      => $userId
        ]);

        // Perbarui data nama di session jika ada
        if (isset($_SESSION['user'])) {
            $_SESSION['user']['name'] = $request['name'];
        }

        $pdo->commit();

        // Redirect kembali ke halaman edit profile
        header('location: ../../pages/admin/profile/edit.php');
        exit;

    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
}
?>