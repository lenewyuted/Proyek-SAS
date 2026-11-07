<?php
require_once __DIR__ . "/../config/database.php";

function getUsers($search, $currentPage, $perPage)
{
    global $pdo;

    $query = "select * from users
            where name like :search
            limit :limit offset :offset";

    $stmt = $pdo->prepare($query);
    $stmt->execute([
        'search' => "%$search%",
        'limit' => $perPage,
        'offset' => ($currentPage -1) * $perPage,
    ]);

    $products = $stmt->fetchAll();

    return $products;
}


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

function getProfile($id)
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

function countUsers()
{
    global $pdo;
    $stmt = $pdo->query("select count(*) as total from users");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['total'] ?? 0;
}


function countOfUsers($search)
{
    global $pdo;

    $query = "select count(id) from users where name like :name";

    $stmt = $pdo->prepare($query);
    $stmt->execute([
        'name' => "%$search%",
    ]);

    $totalUsers = $stmt->fetchColumn();

    return $totalUsers;
}
