<?php
session_start();
require_once __DIR__ . "/../../config/database.php";
require_once __DIR__ . "/../../validations/user-validation.php";

if (isset($_POST['store']) && $_SERVER['REQUEST_METHOD'] === "POST") {
    try {
        $pdo->beginTransaction();

        $request = getUserRequest($_POST);
        $errors = validateUser($request);

        if (count($errors) > 0) {
            $_SESSION['errors'] = $errors; //simpan pesan error ke session (tempat penyimpanan sementara yg berada di server)
            $_SESSION['old'] = $request; //biasa ketika ada validasi error, kebiasaan form akan mereset semua inputannya. Jdi dgn ini bs mempertahankan semua inputannya

            header('location: ../../pages/admin/users/create.php');
            exit;
        }

        $query = "insert into users (name, email, password, role) 
                 values (:name, :email, :password, :role)";


        $stmt = $pdo->prepare($query);
        $stmt->execute([
            'name' => $request['name'],
            'email' => $request['email'],
            'password' => $request['password'],
            'role' => $request['role'],
        ]);

        $pdo->commit(); //ketika sudah slesai, kt akan memastikan bahwa data ini udah bnr jdi ini akan dieksekusi dan dikirim ke databasenya

        header('location: ../../pages/admin/users/index.php');
        exit;
    } catch (\PDOException $err) {
        $pdo->rollBack();
        throw $err;
    }
}
