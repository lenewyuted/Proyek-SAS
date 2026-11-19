<?php
session_start();
require_once __DIR__ . "/../../repositories/order-repository.php";
require_once __DIR__ . "/../../repositories/product-repository.php";

$orderNumber = filter_input(INPUT_GET, 'order', FILTER_SANITIZE_SPECIAL_CHARS);

if (!$orderNumber) {
    header('Location: ../landing-page/index.php');
    exit;
}

$order = getOrderByNumber($orderNumber);
if (!$order) {
    header('Location: ../landing-page/index.php');
    exit;
}

$orderItems = getOrderItemsByOrderId($order['id']);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechHub - Pesanan Berhasil</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kumbh+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="stylesheet" href="../../styles/components/header.css">
    <link rel="stylesheet" href="../../styles/components/footer.css">
    <link rel="stylesheet" href="../../styles/pages/checkout.css">

    <style>
        .success-box {
            background-color: var(--white);
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.06);
            padding: 32px;
            max-width: 800px;
            margin: 40px auto;
        }

        .success-header {
            text-align: center;
            margin-bottom: 24px;
        }

        .success-icon {
            font-size: 54px;
            color: #198754;
            margin-bottom: 12px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 24px;
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
        }

        .items-table th,
        .items-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        .items-table th {
            background: #f1f5f9;
        }

        .badge-status {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            background: #fff3cd;
            color: #856404;
        }
    </style>
</head>

<body>
    <?php require_once __DIR__ . "/../../components/landing/header.php"; ?>

    <main class="checkout-main-container">
        <div class="success-box">
            <div class="success-header">
                <i class="fa-solid fa-circle-check success-icon"></i>
                <h1 style="font-size: 26px; color: var(--text-dark);">Pesanan Anda Berhasil Dibuat!</h1>
                <p style="color: var(--text-muted);">Terima kasih atas pesanan Anda. Kami sedang memproses pengiriman produk Anda.</p>
                <div style="margin-top: 10px;">
                    <span style="font-weight: 700; font-size: 16px;">Nomor Pesanan: <?= htmlspecialchars($order['order_number']) ?></span>
                </div>
            </div>

            <div class="info-grid">
                <div>
                    <h3 style="font-size: 15px; margin-bottom: 8px; color: var(--primary-blue);">Alamat Pengiriman</h3>
                    <p style="margin: 0; font-weight: 600;"><?= htmlspecialchars($order['full_name']) ?> (<?= htmlspecialchars($order['phone_number']) ?>)</p>
                    <p style="margin: 4px 0 0; color: #555;">
                        <?= htmlspecialchars($order['address']) ?><br>
                        <?= htmlspecialchars($order['city']) ?>, <?= htmlspecialchars($order['province']) ?> <?= htmlspecialchars($order['postal_code']) ?>
                    </p>
                </div>
                <div>
                    <h3 style="font-size: 15px; margin-bottom: 8px; color: var(--primary-blue);">Informasi Pengiriman & Pembayaran</h3>
                    <p style="margin: 0;">Layanan: <strong><?= strtoupper(htmlspecialchars($order['shipping_method'])) ?></strong> (<?= formatRupiah($order['shipping_cost']) ?>)</p>
                    <p style="margin: 4px 0 0;">Metode Pembayaran: <strong><?= strtoupper(htmlspecialchars($order['payment_method'])) ?></strong></p>
                    <p style="margin: 4px 0 0;">Status Bayar: <span class="badge-status"><?= strtoupper(htmlspecialchars($order['payment_status'])) ?></span></p>
                </div>
            </div>

            <h3 style="font-size: 17px; margin-bottom: 12px;">Rincian Barang</h3>
            <table class="items-table">
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th style="text-align: center;">Qty</th>
                        <th style="text-align: right;">Harga Satuan</th>
                        <th style="text-align: right;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orderItems as $orderItem): ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars($orderItem['product_name']) ?></strong>
                            </td>
                            <td style="text-align: center;"><?= $orderItem['quantity'] ?></td>
                            <td style="text-align: right;"><?= formatRupiah($orderItem['price']) ?></td>
                            <td style="text-align: right; font-weight: 600;"><?= formatRupiah($orderItem['subtotal']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" style="text-align: right; font-weight: 600;">Ongkos Kirim:</td>
                        <td style="text-align: right; font-weight: 600;"><?= formatRupiah($order['shipping_cost']) ?></td>
                    </tr>
                    <tr style="font-size: 16px;">
                        <td colspan="3" style="text-align: right; font-weight: 700; color: var(--primary-blue);">Total Pembayaran:</td>
                        <td style="text-align: right; font-weight: 700; color: var(--primary-blue);"><?= formatRupiah($order['total_amount']) ?></td>
                    </tr>
                </tfoot>
            </table>

            <div style="text-align: center; margin-top: 30px;">
                <a href="../landing-page/index.php" class="btn-checkout-submit" style="display: inline-block; padding: 12px 28px; text-decoration: none; width: auto;">
                    <i class="fa-solid fa-house"></i> Kembali ke Beranda
                </a>
            </div>
        </div>
    </main>

    <?php require_once __DIR__ . "/../../components/landing/footer.php"; ?>
</body>

</html>