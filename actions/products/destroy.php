<?php
require_once __DIR__ . "/../../config/database.php";

if (isset($_GET['id']) && $_SERVER['REQUEST_METHOD'] === "GET") {
    try {
        $pdo->beginTransaction();
        $id = $_GET['id'];
        //Delete relation data (book author) karena relasinya many to many
        $query = "delete from products where id = :id";
        $stmt = $pdo->prepare($query);
        $stmt->execute([
        'id' => $id,
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
