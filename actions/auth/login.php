<?php
require_once __DIR__ . "/../../middleware/auth.php";
require_once __DIR__ . "/../../config/database.php";
require_once __DIR__ . "/../../validations/auth-validation.php";
require_once __DIR__ . "/../../repositories/user-repository.php";
sessionStart();

if(isset($_POST['login']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $request = getLoginRequest($_POST);
        $errors = validateLogin($request);
        $user = getUserByEmail($request['email']);

        if(count($errors) === 0) {
            if(!$user && !password_verify($request['password'], $user['password'])) {
                $errors['login'] = "Email atau kata sandi salah";
            }
        }

        if(count($errors) > 0) {
            $_SESSION['errors'] = $errors;
            unset($request['password']);
            $_SESSION['old'] = $request;

            header('location: ' . LOGIN_URL);
            exit;
        }

        session_regenerate_id(true); //mengganti id session yg lama, yg dmn jika kt login, kt akan login dgn id session nya 1. ketika kt log out lalu login lagi, maka dia akan regenerate id session yg baru untuk kita, jdi id 1 udh ga bs dipakai 
        $_SESSION['user'] = [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role'],
        ];

        // HYBRID CART: Sinkronkan dan pulihkan (merge & restore) keranjang belanja
        require_once __DIR__ . "/../../repositories/cart-repository.php";
        $guestCartItems = $_SESSION['cart'] ?? [];
        $_SESSION['cart'] = mergeAndRestoreCart($user['id'], $guestCartItems);

        // Jika sebelumnya ada niat checkout, langsung arahkan ke checkout
        $redirectUrl = $_SESSION['redirect_after_login'] ?? LANDING_PAGE;
        unset($_SESSION['redirect_after_login']);

        header("Location: " . $redirectUrl);
        exit;
        
    } catch (PDOException $e) {
        throw $e;

    }
}
?>