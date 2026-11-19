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

        $stockSql = "UPDATE products SET stock = stock - :quantity WHERE id = :product_id";
        $stmtStock = $pdo->prepare($stockSql);

        foreach ($items as $item) {
            $stmtItem->execute([
                'order_id'   => $orderId,
                'product_id' => $item['product_id'],
                'quantity'   => $item['quantity'],
                'price'      => $item['price'],
                'subtotal'   => $item['subtotal'],
            ]);

            // Potong stok produk
            $stmtStock->execute([
                'quantity'   => $item['quantity'],
                'product_id' => $item['product_id'],
            ]);
        }

        // 3. Simpan data pembayaran ke tabel payments
        $paymentSql = "INSERT INTO payments (order_id, payment_method, amount, payment_status)
                       VALUES (:order_id, :payment_method, :amount, 'unpaid')";
        $stmtPayment = $pdo->prepare($paymentSql);
        $stmtPayment->execute([
            'order_id'       => $orderId,
            'payment_method' => $paymentData['payment_method'],
            'amount'         => $orderData['total_amount'],
        ]);

        $pdo->commit();
        return $orderData['order_number'];
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
}

/**
 * Mengambil informasi pesanan berdasarkan nomor pesanan (order_number).
 */
function getOrderByNumber($orderNumber)
{
    global $pdo;

    $query = "SELECT o.*, p.payment_method, p.payment_status 
              FROM orders o
              LEFT JOIN payments p ON p.order_id = o.id
              WHERE o.order_number = :order_number";
    $stmt  = $pdo->prepare($query);
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
    $stmt  = $pdo->prepare($query);
    $stmt->execute([
        'order_id' => $orderId
    ]);
    return $stmt->fetchAll();
}

function getOrders()
{
    global $pdo;

    $query = "select * from orders";
    $stmt = $pdo->prepare($query);
    $stmt->execute();

    return $stmt->fetchAll();
}
