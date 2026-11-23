<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../../config/database.php";

$profileRedirectUrl = "../../pages/profile/index.php";
$loginRedirectUrl   = "../../pages/auth/login.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: " . $profileRedirectUrl);
    exit;
}

$userId = $_SESSION["user"]["id"] ?? $_SESSION["user_id"] ?? $_SESSION["id"] ?? null;
if (!$userId) {
    header("Location: " . $loginRedirectUrl);
    exit;
}

$userId = (int) $userId;

try {
    $pdo->beginTransaction();

    $stmtCurrent = $pdo->prepare("SELECT * FROM users WHERE id = :id LIMIT 1");
    $stmtCurrent->execute(["id" => $userId]);
    $currentUser = $stmtCurrent->fetch(PDO::FETCH_ASSOC);

    if (!$currentUser) {
        throw new Exception("Data user tidak ditemukan.");
    }

    $columnsStmt        = $pdo->query("SHOW COLUMNS FROM users");
    $availableCols      = $columnsStmt->fetchAll(PDO::FETCH_COLUMN);
    $availableColsLower = array_map("strtolower", $availableCols);

    $setParts = [];
    $params   = ["id" => $userId];

    // Email & role sengaja tidak disentuh agar email tidak pernah terhapus atau berubah
    if (in_array("phone_number", $availableColsLower, true)) {
        $setParts[]             = "phone_number = :phone_number";
        $params["phone_number"] = "";
    } elseif (in_array("phone", $availableColsLower, true)) {
        $setParts[]      = "phone = :phone";
        $params["phone"] = "";
    }

    if (in_array("gender", $availableColsLower, true)) {
        $setParts[]       = "gender = :gender";
        $params["gender"] = null;
    }

    if (in_array("province", $availableColsLower, true)) {
        $setParts[]         = "province = :province";
        $params["province"] = "";
    }

    if (in_array("city", $availableColsLower, true)) {
        $setParts[]     = "city = :city";
        $params["city"] = "";
    }

    if (in_array("address", $availableColsLower, true)) {
        $setParts[]        = "address = :address";
        $params["address"] = "";
    }

    if (!empty($setParts)) {
        $query = "UPDATE users SET " . implode(", ", $setParts) . " WHERE id = :id";
        $stmt  = $pdo->prepare($query);
        $stmt->execute($params);
    }

    if (isset($_SESSION["user"]) && is_array($_SESSION["user"])) {
        $_SESSION["user"]["phone_number"] = "";
        $_SESSION["user"]["gender"]       = null;
        $_SESSION["user"]["province"]     = "";
        $_SESSION["user"]["city"]         = "";
        $_SESSION["user"]["address"]      = "";
    }

    unset($_SESSION["old"], $_SESSION["errors"]);
    $pdo->commit();

    $_SESSION["flash_success"] = "Data profil berhasil dikosongkan.";
    header("Location: " . $profileRedirectUrl);
    exit;
} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    $_SESSION["flash_error"] = "Gagal mereset profil: " . $e->getMessage();
    header("Location: " . $profileRedirectUrl);
    exit;
}
