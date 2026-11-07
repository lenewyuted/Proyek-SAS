<?php
require_once __DIR__ . "/../../config/database.php";

if (isset($_GET['id']) && $_SERVER['REQUEST_METHOD'] === "GET") {
    try {
        $pdo->beginTransaction();
        $id = $_GET['id'];
        $query = "delete from users where id = :id";
        $stmt = $pdo->prepare($query);
        $stmt->execute([
        'id' => $id,
        ]);

        $pdo->commit();

        header('location: ../../pages/admin/users/index.php');
        exit;
    } catch (PDOException $e) {
        $pdo->rollBack();
        throw $e;
    }
}
?>


















































