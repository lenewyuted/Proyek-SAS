<?php
require_once __DIR__ . "/../config/database.php";

/**
 * Membuat pesanan baru lengkap beserta detail produk, pembayaran, dan pengurangan stok dalam 1 transaksi DB.
 */
function createOrder(array $orderData, array $items, array $paymentData)
{
    global $pdo;

    $pdo->beginTransaction();

    try {
        // 1. Simpan ke tabel orders
        $orderSql = "INSERT INTO orders (
                        user_id, order_number, full_name, phone_number,
                        address, city, province, postal_code,
                        shipping_method, shipping_cost, total_amount, order_status
                    ) VALUES (
                        :user_id, :order_number, :full_name, :phone_number,
                        :address, :city, :province, :postal_code,
                        :shipping_method, :shipping_cost, :total_amount, 'pending'
                    )";
        $stmtOrder = $pdo->prepare($orderSql);
        $stmtOrder->execute([
            'user_id'         => $orderData['user_id'],
            'order_number'    => $orderData['order_number'],
            'full_name'       => $orderData['full_name'],
            'phone_number'    => $orderData['phone_number'],
            'address'         => $orderData['address'],
            'city'            => $orderData['city'],
            'province'        => $orderData['province'],
            'postal_code'     => $orderData['postal_code'],
            'shipping_method' => $orderData['shipping_method'],
            'shipping_cost'   => $orderData['shipping_cost'],
            'total_amount'    => $orderData['total_amount'],
        ]);

        $orderId = (int) $pdo->lastInsertId();

        // 2. Simpan setiap barang ke order_items & kurangi stok produk
        $itemSql = "INSERT INTO order_items (order_id, product_id, quantity, price, subtotal)
                    VALUES (:order_id, :product_id, :quantity, :price, :subtotal)";
        $stmtItem = $pdo->prepare($itemSql);

        $stockSql = "UPDATE products
                     SET stock = stock - :qty_sub
                     WHERE id = :product_id AND stock >= :qty_min";
        $stmtStock = $pdo->prepare($stockSql);

        foreach ($items as $item) {
            $stmtItem->execute([
                'order_id'   => $orderId,
                'product_id' => $item['product_id'],
                'quantity'   => $item['quantity'],
                'price'      => $item['price'],
                'subtotal'   => $item['subtotal'],
            ]);

            // Potong stok produk & pastikan stok mencukupi
            $stmtStock->execute([
                'qty_sub'    => $item['quantity'],
                'product_id' => $item['product_id'],
                'qty_min'    => $item['quantity'],
            ]);

            if ($stmtStock->rowCount() === 0) {
                throw new RuntimeException("Stok produk dengan ID {$item['product_id']} tidak mencukupi.");
            }
        }

        // 3. Simpan data pembayaran ke tabel payments
        $paymentStatus = !empty($paymentData['payment_proof']) ? 'pending_verification' : 'unpaid';

        $paymentSql = "INSERT INTO payments (
                           order_id, payment_method, payment_channel,
                           account_number, account_holder, amount,
                           payment_status, payment_proof
                       ) VALUES (
                           :order_id, :payment_method, :payment_channel,
                           :account_number, :account_holder, :amount,
                           :payment_status, :payment_proof
                       )";
        $stmtPayment = $pdo->prepare($paymentSql);
        $stmtPayment->execute([
            'order_id'        => $orderId,
            'payment_method'  => $paymentData['payment_method'],
            'payment_channel' => $paymentData['payment_channel'] ?? null,
            'account_number'  => $paymentData['account_number'] ?? null,
            'account_holder'  => $paymentData['account_holder'] ?? null,
            'amount'          => $orderData['total_amount'],
            'payment_status'  => $paymentStatus,
            'payment_proof'   => $paymentData['payment_proof'] ?? null,
        ]);

        $pdo->commit();
        return $orderData['order_number'];
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $e;
    }
}

/**
 * Mengambil informasi pesanan berdasarkan nomor pesanan (order_number).
 */
function getOrderByNumber($orderNumber)
{
    global $pdo;

    $query = "SELECT o.*, p.payment_method, p.payment_channel, p.account_number, p.account_holder, p.payment_status, p.payment_proof
              FROM orders o
              LEFT JOIN payments p ON p.order_id = o.id
              WHERE o.order_number = :order_number";
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        'order_number' => $orderNumber
    ]);
    return $stmt->fetch();
}

/**
 * Mengambil semua rincian produk pada pesanan tertentu.
 */
function getOrderItemsByOrderId($orderId)
{
    global $pdo;

    $query = "SELECT oi.*, p.name AS product_name, p.picture AS product_picture
              FROM order_items oi
              JOIN products p ON p.id = oi.product_id
              WHERE oi.order_id = :order_id";
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        'order_id' => $orderId
    ]);
    return $stmt->fetchAll();
}

/**
 * Mengambil seluruh data pesanan.
 */
function getOrders()
{
    global $pdo;

    $query = "SELECT * FROM orders";
    $stmt = $pdo->prepare($query);
    $stmt->execute();

    return $stmt->fetchAll();
}

/**
 * Mengambil satu data pesanan berdasarkan ID.
 */
function getOrderById($id)
{
    global $pdo;

    $query = "SELECT * FROM orders WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        'id' => $id
    ]);

    return $stmt->fetch();
}

/**
 * Memperbarui data pesanan berdasarkan ID.
 */
function updateOrder($id, array $data)
{
    global $pdo;

    $query = "UPDATE orders SET
                full_name = :full_name,
                phone_number = :phone_number,
                address = :address,
                city = :city,
                province = :province,
                postal_code = :postal_code,
                shipping_method = :shipping_method,
                shipping_cost = :shipping_cost,
                total_amount = :total_amount
              WHERE id = :id";

    $stmt = $pdo->prepare($query);
    return $stmt->execute([
        'id'              => $id,
        'full_name'       => $data['full_name'],
        'phone_number'    => $data['phone_number'],
        'address'         => $data['address'],
        'city'            => $data['city'],
        'province'        => $data['province'],
        'postal_code'     => $data['postal_code'],
        'shipping_method' => $data['shipping_method'],
        'shipping_cost'   => $data['shipping_cost'],
        'total_amount'    => $data['total_amount'],
    ]);
}

/**
 * Memperbarui status pesanan berdasarkan ID.
 */
function updateOrderStatus(int $id, string $newStatus): bool
{
    global $pdo;

    $allowedStatuses = ['pending', 'processing', 'shipped', 'completed', 'cancelled'];
    if (!in_array($newStatus, $allowedStatuses, true)) {
        return false;
    }

    $query = "UPDATE orders SET order_status = :order_status WHERE id = :id";
    $stmt = $pdo->prepare($query);
    return $stmt->execute([
        'id'           => $id,
        'order_status' => $newStatus,
    ]);
}

function getOrdersByUserId(int $userId, string $statusFilter = 'all'): array
{
    global $pdo;

    $sql = "
        SELECT o.*, p.payment_method, p.payment_channel, p.payment_status 
        FROM orders o 
        LEFT JOIN payments p ON o.id = p.order_id 
        WHERE o.user_id = :user_id
    ";

    $params = ['user_id' => $userId];

    if ($statusFilter !== 'all') {
        $sql .= " AND LOWER(o.order_status) = :status";
        $params['status'] = $statusFilter;
    }

    $sql .= " ORDER BY o.created_at DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function cancelOrder(int $orderId, int $userId): bool
{
    global $pdo;

    try {
        $pdo->beginTransaction();

        // 1. Kunci baris order untuk mencegah race condition & pastikan status masih pending
        $stmtOrder = $pdo->prepare("SELECT id, order_status FROM orders WHERE id = :id AND user_id = :user_id FOR UPDATE");
        $stmtOrder->execute([
            'id'      => $orderId,
            'user_id' => $userId,
        ]);
        $order = $stmtOrder->fetch(PDO::FETCH_ASSOC);

        if (!$order || strtolower($order['order_status']) !== 'pending') {
            $pdo->rollBack();
            return false;
        }

        // 2. Ambil item pesanan untuk mengembalikan stok produk
        $stmtItems = $pdo->prepare("SELECT product_id, quantity FROM order_items WHERE order_id = :order_id");
        $stmtItems->execute(['order_id' => $orderId]);
        $items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);

        $stmtRestoreStock = $pdo->prepare("UPDATE products SET stock = stock + :qty WHERE id = :product_id");
        foreach ($items as $item) {
            $stmtRestoreStock->execute([
                'qty'        => (int) $item['quantity'],
                'product_id' => (int) $item['product_id'],
            ]);
        }

        // 3. Ubah status order menjadi cancelled
        $stmtUpdateOrder = $pdo->prepare("UPDATE orders SET order_status = 'cancelled' WHERE id = :id");
        $stmtUpdateOrder->execute(['id' => $orderId]);

        $pdo->commit();
        return true;
    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $e;
    }
}
