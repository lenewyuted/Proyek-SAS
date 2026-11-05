<?php
require_once __DIR__ . "/../../middleware/auth.php";
require_once __DIR__ . "/../../config/database.php";
require_once __DIR__ . "/../../repositories/user-repository.php";
require_once __DIR__ . "/../../validations/profile-validation.php";

if (isset($_POST['update']) && $_SERVER['REQUEST_METHOD'] === "POST") {
    try {
        $pdo->beginTransaction();

        $userId = getAuthUserId(); // id admin yang sedang login (bukan dari form, supaya tidak bisa diubah pengguna)

        // ketika panggil function ini, function ini akan menerima inputan dari pengguna
        $request = getProfileRequest($_POST); // berisi inputan dari pengguna

        $errors = validateProfile($request, $userId); // untuk menampung pesan terkait dengan inputan yang mau kita validasi

        if (count($errors) > 0) {
            $_SESSION['errors'] = $errors; // simpan pesan error ke session (tempat penyimpanan sementara di server)
            $_SESSION['old'] = $request; // supaya inputan pengguna tidak hilang ketika validasi gagal

            $pdo->rollBack();

            header('location: ../../pages/admin/profile.php');
            exit;
        }

        // Ambil foto lama dari database
        $user = getUser($userId);
        $photoPath = $user['photo'];

        // LOGIKA UPLOAD FOTO BARU
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {

            // Tentukan lokasi folder penyimpanan, buat foldernya jika belum ada
            $uploadDir = __DIR__ . "/../../asset/uploads/";

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            // Buat nama file baru supaya tidak bentrok dengan foto pengguna lain
            $extension = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
            $newName = 'user_' . $userId . '_' . time() . '.' . $extension;

            if (move_uploaded_file($_FILES['photo']['tmp_name'], $uploadDir . $newName)) {
                // Hapus foto lama agar tidak menumpuk di folder
                if (!empty($photoPath) && is_file($uploadDir . $photoPath)) {
                    unlink($uploadDir . $photoPath);
                }

                $photoPath = $newName;
            }
        }

        // Update main data
        $query = "update users set full_name = :full_name, username = :username, email = :email, phone = :phone, address = :address, bio = :bio, photo = :photo where id = :id";

        $stmt = $pdo->prepare($query);
        $stmt->execute([
            'full_name' => $request['full_name'],
            'username' => $request['username'],
            'email' => $request['email'],
            'phone' => $request['phone'],
            'address' => $request['address'],
            'bio' => $request['bio'],
            'photo' => $photoPath,
            'id' => $userId,
        ]);

        $pdo->commit();

        $_SESSION['success'] = "Profil berhasil diperbarui.";

        header('location: ../../pages/admin/profile.php');
        exit;

    } catch (PDOException $e) {
        $pdo->rollBack();
        throw $e;
    }
}
?>
