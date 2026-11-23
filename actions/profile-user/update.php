<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../../config/database.php";

$profileRedirectUrl = "../../pages/profile/index.php";
$loginRedirectUrl   = "../../pages/auth/login.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_POST["update"])) {
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

    // Email sengaja tidak diambil dari $_POST agar tidak pernah bisa diubah atau dihapus
    $name        = trim((string) ($_POST["name"] ?? ""));
    $phoneNumber = trim((string) ($_POST["phone_number"] ?? ""));
    $gender      = strtolower(trim((string) ($_POST["gender"] ?? "")));
    $province    = trim((string) ($_POST["province"] ?? ""));
    $city        = trim((string) ($_POST["city"] ?? ""));
    $address     = trim((string) ($_POST["address"] ?? ""));

    $errors = [];

    if ($name === "") {
        $errors["name"] = "Name wajib diisi.";
    } elseif (mb_strlen($name) > 120) {
        $errors["name"] = "Name maksimal 120 karakter.";
    }

    if ($phoneNumber !== "" && !preg_match("/^[0-9+\-\s]{6,20}$/", $phoneNumber)) {
        $errors["phone_number"] = "Format nomor telepon tidak valid.";
    }

    if ($gender !== "" && !in_array($gender, ["female", "male", "secret"], true)) {
        $errors["gender"] = "Pilihan gender tidak valid.";
    }

    if (mb_strlen($province) > 100) {
        $errors["province"] = "Province maksimal 100 karakter.";
    }

    if (mb_strlen($city) > 100) {
        $errors["city"] = "City maksimal 100 karakter.";
    }

    if (mb_strlen($address) > 255) {
        $errors["address"] = "Address maksimal 255 karakter.";
    }

    $currentLower    = array_change_key_case($currentUser, CASE_LOWER);
    $pictureFilename = $currentLower["picture"] ?? ($currentLower["avatar"] ?? null);

    if (
        !empty($_FILES["picture"]["name"]) &&
        (int) ($_FILES["picture"]["error"] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK
    ) {
        $tmpName = (string) $_FILES["picture"]["tmp_name"];
        $size    = (int) ($_FILES["picture"]["size"] ?? 0);
        $ext     = strtolower(pathinfo((string) $_FILES["picture"]["name"], PATHINFO_EXTENSION));
        $allowed = ["jpg", "jpeg", "png", "webp"];

        if (!in_array($ext, $allowed, true)) {
            $errors["picture"] = "Format foto harus JPG, JPEG, PNG, atau WEBP.";
        } elseif ($size <= 0 || $size > 2 * 1024 * 1024) {
            $errors["picture"] = "Ukuran foto maksimal 2MB.";
        } else {
            $uploadDir = __DIR__ . "/../../assets/img/avatars/";
            if (!is_dir($uploadDir) && !mkdir($uploadDir, 0775, true) && !is_dir($uploadDir)) {
                $errors["picture"] = "Gagal menyiapkan folder penyimpanan foto.";
            } else {
                $newFilename = "avatar_" . $userId . "_" . time() . "." . $ext;
                if (move_uploaded_file($tmpName, $uploadDir . $newFilename)) {
                    $pictureFilename = $newFilename;
                } else {
                    $errors["picture"] = "Gagal mengunggah foto profil.";
                }
            }
        }
    }

    if (!empty($errors)) {
        $pdo->rollBack();
        $_SESSION["errors"]      = $errors;
        $_SESSION["flash_error"] = reset($errors);
        $_SESSION["old"]         = [
            "name"         => $name,
            "phone_number" => $phoneNumber,
            "gender"       => $gender,
            "province"     => $province,
            "city"         => $city,
            "address"      => $address,
            "picture"      => $pictureFilename,
        ];
        header("Location: " . $profileRedirectUrl);
        exit;
    }

    $columnsStmt        = $pdo->query("SHOW COLUMNS FROM users");
    $availableCols      = $columnsStmt->fetchAll(PDO::FETCH_COLUMN);
    $availableColsLower = array_map("strtolower", $availableCols);

    $setParts = [];
    $params   = ["id" => $userId];

    if (in_array("name", $availableColsLower, true)) {
        $setParts[]     = "name = :name";
        $params["name"] = $name;
    }

    if (in_array("phone_number", $availableColsLower, true)) {
        $setParts[]             = "phone_number = :phone_number";
        $params["phone_number"] = $phoneNumber;
    } elseif (in_array("phone", $availableColsLower, true)) {
        $setParts[]      = "phone = :phone";
        $params["phone"] = $phoneNumber;
    }

    if (in_array("gender", $availableColsLower, true)) {
        $setParts[]       = "gender = :gender";
        $params["gender"] = $gender !== "" ? $gender : null;
    }

    if (in_array("province", $availableColsLower, true)) {
        $setParts[]         = "province = :province";
        $params["province"] = $province;
    }

    if (in_array("city", $availableColsLower, true)) {
        $setParts[]     = "city = :city";
        $params["city"] = $city;
    }

    if (in_array("address", $availableColsLower, true)) {
        $setParts[]        = "address = :address";
        $params["address"] = $address;
    }

    if ($pictureFilename !== null) {
        if (in_array("picture", $availableColsLower, true)) {
            $setParts[]        = "picture = :picture";
            $params["picture"] = $pictureFilename;
        } elseif (in_array("avatar", $availableColsLower, true)) {
            $setParts[]       = "avatar = :avatar";
            $params["avatar"] = $pictureFilename;
        }
    }

    if (!empty($setParts)) {
        $query = "UPDATE users SET " . implode(", ", $setParts) . " WHERE id = :id";
        $stmt  = $pdo->prepare($query);
        $stmt->execute($params);
    }

    if (isset($_SESSION["user"]) && is_array($_SESSION["user"])) {
        $_SESSION["user"]["name"]         = $name;
        $_SESSION["user"]["phone_number"] = $phoneNumber;
        $_SESSION["user"]["gender"]       = $gender !== "" ? $gender : null;
        $_SESSION["user"]["province"]     = $province;
        $_SESSION["user"]["city"]         = $city;
        $_SESSION["user"]["address"]      = $address;
        if ($pictureFilename !== null) {
            $_SESSION["user"]["picture"] = $pictureFilename;
        }
    }

    $pdo->commit();

    $_SESSION["flash_success"] = "Profil berhasil diperbarui.";
    header("Location: " . $profileRedirectUrl);
    exit;
} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    $_SESSION["flash_error"] = "Gagal menyimpan profil: " . $e->getMessage();
    header("Location: " . $profileRedirectUrl);
    exit;
}
