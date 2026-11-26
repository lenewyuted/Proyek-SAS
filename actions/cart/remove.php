<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config/database.php';

// Hanya menerima request POST untuk keamanan aksi hapus
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../pages/user/cart/index.php');
    exit;
}

$userId = (int) ($_SESSION['user']['id'] ?? $_SESSION['user_id'] ?? $_SESSION['id'] ?? 0);

// Helper hapus di database jika user login
function removeCartFromDb(PDO $pdo, int $userId, ?int $productId = null): void
{
    if ($userId <= 0) {
        return;
    }

    // Sesuaikan nama tabel bila proyekmu memakai nama lain, misalnya 'cart' atau 'cart_items'
    $tableName = null;
    foreach (['cart', 'cart_items', 'carts'] as $candidate) {
        $check = $pdo->query("SHOW TABLES LIKE " . $pdo->quote($candidate));
        if ($check && $check->fetchColumn()) {
            $tableName = $candidate;
            break;
        }
    }

    if ($tableName === null) {
        return;
    }

    if ($productId === null) {
        $stmt = $pdo->prepare("DELETE FROM `{$tableName}` WHERE user_id = :user_id");
        $stmt->execute([':user_id' => $userId]);
    } else {
        $stmt = $pdo->prepare("DELETE FROM `{$tableName}` WHERE user_id = :user_id AND product_id = :product_id");
        $stmt->execute([
            ':user_id'    => $userId,
            ':product_id' => $productId,
        ]);
    }
}

try {
    if (isset($pdo) && $pdo instanceof PDO) {
        $pdo->beginTransaction();
    }

    // Opsi A: Hapus Semua Barang di Keranjang (Remove All)
    if (isset($_POST['remove_all'])) {
        unset($_SESSION['cart']);

        if (isset($pdo) && $pdo instanceof PDO && $userId > 0) {
            removeCartFromDb($pdo, $userId, null);
            $pdo->commit();
        }

        $_SESSION['flash_success'] = 'Semua produk berhasil dihapus dari keranjang.';
        header('Location: ../../pages/user/cart/index.php');
        exit;
    }

    // Opsi B: Hapus 1 Produk Spesifik
    $productId = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);

    if ($productId && $productId > 0) {
        if (isset($_SESSION['cart'][$productId])) {
            unset($_SESSION['cart'][$productId]);
        } elseif (!empty($_SESSION['cart']) && is_array($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $key => $item) {
                $itemProdId = (int) ($item['product_id'] ?? $item['id'] ?? $key);
                if ($itemProdId === $productId) {
                    unset($_SESSION['cart'][$key]);
                }
            }
        }

        if (isset($pdo) && $pdo instanceof PDO && $userId > 0) {
            removeCartFromDb($pdo, $userId, $productId);
            $pdo->commit();
        }

        $_SESSION['flash_success'] = 'Produk berhasil dihapus dari keranjang.';
    } elseif (isset($pdo) && $pdo instanceof PDO && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
} catch (Throwable $e) {
    if (isset($pdo) && $pdo instanceof PDO && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    $_SESSION['flash_error'] = 'Gagal menghapus produk dari keranjang.';
}

header('Location: ../../pages/user/cart/index.php');
exit;
