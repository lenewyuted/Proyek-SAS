<?php
require_once __DIR__ . "/../config/database.php";

function isEmailTaken($email)
{
  global $pdo;

  $query = "select count(id) from users where email = :email";

  $stmt = $pdo->prepare($query);
  $stmt->execute([
    'email' => $email
  ]);

  $isEmailTaken = $stmt->fetchColumn() > 0;

  return $isEmailTaken;
}

function getUserByEmail($email) 
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

function countUsers() {
    global $pdo; 
    $stmt = $pdo->query("select count(*) as total from users");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['total'] ?? 0;
}
?>
