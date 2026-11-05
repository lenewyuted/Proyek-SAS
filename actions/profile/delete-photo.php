<?php
require_once __DIR__ . "/../../middleware/auth.php";
require_once __DIR__ . "/../../config/database.php";
require_once __DIR__ . "/../../repositories/user-repository.php";

if (isset($_POST['delete_photo']) && $_SERVER['REQUEST_METHOD'] === "POST") {
    try {
        $pdo->beginTransaction();

        $userId = getAuthUserId();
        $user = getUser($userId);

        // Hapus file foto dari folder uploads
        $uploadDir = __DIR__ . "/../../asset/uploads/";

        if (!empty($user['photo']) && is_file($uploadDir . $user['photo'])) {
            unlink($uploadDir . $user['photo']);
        }

        $query = "update users set photo = null where id = :id";
        $stmt = $pdo->prepare($query);
        $stmt->execute([
            'id' => $userId,
        ]);

        $pdo->commit();

        $_SESSION['success'] = "Foto profil dihapus.";

        header('location: ../../pages/admin/profile.php');
        exit;

    } catch (PDOException $e) {
        $pdo->rollBack();
        throw $e;
    }
}
?>
