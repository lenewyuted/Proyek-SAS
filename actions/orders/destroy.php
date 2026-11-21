<?php
require_once __DIR__ . "/../../config/database.php";

if (isset($_GET['id']) && $_SERVER['REQUEST_METHOD'] === "GET") {
    try {
        $pdo->beginTransaction();
        $id = $_GET['id'];
        $query = "delete from orders where id = :id";
        $stmt = $pdo->prepare($query);
        $stmt->execute([
            'id' => $id,
        ]);

        $pdo->commit();

        header('location: ../../pages/admin/order-data/index.php');
        exit;
    } catch (PDOException $e) {
        $pdo->rollBack();
        throw $e;
    }
}
