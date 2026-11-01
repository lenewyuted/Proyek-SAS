<?php
require_once __DIR__ . "/../../config/database.php";
require_once __DIR__ . "/../../validations/product-validation.php";

if(isset($_POST['store']) && $_SERVER['REQUEST_METHOD'] === "POST") {
    try {
        $pdo -> beginTransaction();

        $request = getProductRequest($_POST);
        $errors = validateProduct($request);

        if(count($errors) > 0) {
            $_SESSION['errors'] = $errors; //simpan pesan error ke session (tempat penyimpanan sementara yg berada di server)
            $_SESSION['old'] = $request; //biasa ketika ada validasi error, kebiasaan form akan mereset semua inputannya. Jdi dgn ini bs mempertahankan semua inputannya
            
            header('location: ../../pages/admin/products/create.php');
            exit;
            
        }

        $query = "insert into products (name, description, price, stock, picture, category_id) 
                 values (:name, :description, :price, :stock, :picture, :category_id)"; 

        // LOGIKA UPLOAD FILE
        $picturePath = null;
        if (isset($_FILES['picture']) && $_FILES['picture']['name']) {
            // 1. Ambil nama asli file
            $originalName = $_FILES['picture']['name'];
            // 2. Normalisasi: Ganti semua spasi dengan underscore (_)
            $safeName = str_replace(' ', '_', $originalName);
            // 3. Tentukan lokasi folder dan gabungkan dengan nama yang sudah aman
            $picturePath = "../../assets/img/" . $safeName;
            move_uploaded_file($_FILES['picture']['tmp_name'], $picturePath);
        }

        $stmt = $pdo->prepare($query);
        $stmt -> execute([
            'name' => $request['name'],
            'description' => $request['description'],
            'price' => $request['price'],
            'stock' => $request['stock'],
            'picture' => $picturePath,
            'category_id' => $request['category_id'],
        ]);
        
        $pdo->commit(); //ketika sudah slesai, kt akan memastikan bahwa data ini udah bnr jdi ini akan dieksekusi dan dikirim ke databasenya

        header('location: ../../pages/admin/products/index.php');
        exit;

    } catch (\PDOException $err){
        $pdo->rollBack();
        throw $err;
    }
    
}
?>