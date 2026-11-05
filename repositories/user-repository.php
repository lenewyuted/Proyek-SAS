<?php
require_once __DIR__ . "/../config/database.php";


function getUsers()
{
  global $pdo;

  $query = "select * from users";

  $stmt = $pdo -> prepare($query);
  $stmt -> execute();

  $users = $stmt -> fetchAll();

  return $users;
}

function getUser($id)
{
  global $pdo;

  $query = "select * from users where id = :id";

  $stmt = $pdo->prepare($query);
  $stmt -> execute([
    'id' => $id
  ]);

  $user = $stmt -> fetch(PDO::FETCH_ASSOC);

  if(!$user) {
    header('location: ../pages/admin/users/index.php');
    exit;
  }

  return $user;
}

function getProfile($id)
{
    global $pdo;

    $query = "SELECT * FROM users WHERE id = :id";

    $stmt = $pdo->prepare($query);
    $stmt->execute([
        'id' => $id
    ]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    return $user;
}


function isEmailTaken($email)
{
    global $pdo;

    $query = "select * from users where email = :email";

    $stmt = $pdo->prepare($query);
    $stmt->execute([
        'email' => $email
    ]);

    $user = $stmt->fetch();

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




function countOfUsers($search) 
{
    global $pdo;

    $query = "select count(id) from users where name like :name";

    $stmt = $pdo -> prepare($query);
    $stmt->execute([
        'name' => "%$search%",
    ]);

    $totalUsers = $stmt -> fetchColumn();
    
    return $totalUsers;
}
?>
