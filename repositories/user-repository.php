<?php
require_once __DIR__ . "/../config/database.php";

// Mengambil 1 pengguna berdasarkan id. Jika tidak ketemu, kembalikan false
function getUser($id)
{
    global $pdo;

    $query = "select * from users where id = :id";

    $stmt = $pdo->prepare($query);
    $stmt->execute([
        'id' => $id
    ]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    return $user;
}

// Mengecek apakah username sudah dipakai pengguna lain (id $ignoreId dikecualikan)
function isUsernameTaken($username, $ignoreId = 0)
{
    global $pdo;

    $query = "select count(id) from users where username = :username and id != :id";

    $stmt = $pdo->prepare($query);
    $stmt->execute([
        'username' => $username,
        'id' => $ignoreId,
    ]);

    return $stmt->fetchColumn() > 0;
}

// Mengecek apakah email sudah dipakai pengguna lain (id $ignoreId dikecualikan)
function isEmailTaken($email, $ignoreId = 0)
{
    global $pdo;

    $query = "select count(id) from users where email = :email and id != :id";

    $stmt = $pdo->prepare($query);
    $stmt->execute([
        'email' => $email,
        'id' => $ignoreId,
    ]);

    return $stmt->fetchColumn() > 0;
}
?>
