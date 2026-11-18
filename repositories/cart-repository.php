<?php
require_once __DIR__ . "/../config/database.php";

/**
 * Mengambil semua data keranjang belanja milik user dari database
 * dan diformat sesuai standar Model A: [productId => ['product_id' => ..., 'quantity' => ...]]
 */
function getUserCartFromDb($userId)
{
    global $pdo;

    $query = "SELECT product_id, quantity FROM carts WHERE user_id = :user_id";
    $stmt  = $pdo->prepare($query);
    $stmt->execute(['user_id' => $userId]);
    $rows = $stmt->fetchAll();

    $cart = [];
    foreach ($rows as $row) {
        $productId = (int) $row['product_id'];
        $cart[$productId] = [
            'product_id' => $productId,
            'quantity'   => (int) $row['quantity'],
        ];
    }

    return $cart;
}

/**
 * Menyinkronkan penambahan produk ke database untuk user yang sedang login (UPSERT).
 */
function syncItemToDbCart($userId, $productId, $quantity)
{
    global $pdo;

    $query = "INSERT INTO carts (user_id, product_id, quantity)
              VALUES (:user_id, :product_id, :quantity)
              ON DUPLICATE KEY UPDATE quantity = quantity + :add_quantity";

            

    $stmt = $pdo->prepare($query);

    // execute() mengembalikan nilai boolean: true jika query berhasil disimpan, false jika gagal
    $isSuccess = $stmt->execute([
        'user_id'      => $userId,
        'product_id'   => $productId,
        'quantity'     => $quantity,
        'add_quantity' => $quantity,
    ]);

    return $isSuccess;
}

/**
 * Menghapus 1 produk dari database keranjang user.
 */
function removeItemFromDbCart($userId, $productId)
{
    global $pdo;

    $query = "DELETE FROM carts WHERE user_id = :user_id AND product_id = :product_id";
    $stmt  = $pdo->prepare($query);

    $isDeleted = $stmt->execute([
        'user_id'    => $userId,
        'product_id' => $productId,
    ]);

    return $isDeleted;
}

/**
 * Menghapus seluruh produk di keranjang user (saat Remove All atau setelah Checkout selesai).
 */
function clearUserCartFromDb($userId)
{
    global $pdo;

    $query = "DELETE FROM carts WHERE user_id = :user_id";
    $stmt  = $pdo->prepare($query);

    $isCleared = $stmt->execute(['user_id' => $userId]);

    return $isCleared;
}

/**
 * Menggabungkan (Merge) isi keranjang guest di session dengan keranjang user di database,
 * lalu mengembalikan hasil gabungan untuk dipulihkan ke $_SESSION['cart'].
 */
function mergeAndRestoreCart(string $userId, array $guestCartItems)
{
    global $pdo;

    $userCartFromDb = getUserCartFromDb($userId); 

    // Jika ada barang yang dimasukkan saat belum login (guest), lakukan penggabungan
    if (!empty($guestCartItems)) {
        foreach ($guestCartItems as $productId => $guestCartItem) {
            // foreach ($array as $key => $value)
            // Contoh bentuk array $guestCartItems di session:
            // [
            //     5 => [ // <-- 5 adalah $productId (Key)
            //         'product_id' => 5,
            //         'quantity'   => 2
            //     ],
            //     12 => [ // <-- 12 adalah $productId (Key)
            //         'product_id' => 12,
            //         'quantity'   => 1
            //     ]
            // ]
            $qty = (int) $guestCartItem['quantity'];

            if (isset($userCartFromDb[$productId])) {
                // Jika di DB sudah ada barang yang sama, akumulasikan
                $newQty = $userCartFromDb[$productId]['quantity'] + $qty;
                $userCartFromDb[$productId]['quantity'] = $newQty;

                $queryUpdate = "UPDATE carts SET quantity = :quantity WHERE user_id = :user_id AND product_id = :product_id";

                $updateStmt = $pdo->prepare($queryUpdate);
                $updateStmt->execute([
                    'quantity'   => $newQty,
                    'user_id'    => $userId,
                    'product_id' => $productId,
                ]);
            } else {
                $userCartFromDb[$productId] = [
                    'product_id' => (int) $productId,
                    'quantity'   => $qty,
                ];

                $queryInsert = "INSERT INTO carts (user_id, product_id, quantity) VALUES (:user_id, :product_id, :quantity)";

                $insertStmt = $pdo->prepare($queryInsert);
                $insertStmt->execute([
                    'user_id'    => $userId,
                    'product_id' => $productId,
                    'quantity'   => $qty,
                ]);
            }
        }
    }

    return $userCartFromDb;
}
