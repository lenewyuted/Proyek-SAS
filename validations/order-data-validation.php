<?php

function validateCheckoutData(array $postData, array $fileData): array
{
    $errors = [];

    // 1. Sanitasi Input Dasar
    $fullName       = trim($postData['full_name'] ?? '');
    $phoneNumber    = trim($postData['phone_number'] ?? '');
    $address        = trim($postData['address'] ?? '');
    $city           = trim($postData['city'] ?? '');
    $province       = trim($postData['province'] ?? '');
    $postalCode     = trim($postData['postal_code'] ?? '');
    $shippingMethod = trim($postData['shipping_method'] ?? '');
    $paymentMethod  = trim($postData['payment_method'] ?? '');

    // 2. Validasi Alamat Pengiriman
    if (empty($fullName) || empty($phoneNumber) || empty($address) || empty($city) || empty($province) || empty($postalCode)) {
        $errors[] = 'Harap lengkapi semua data alamat pengiriman.';
    }

    if (!empty($phoneNumber) && !preg_match('/^[0-9]{10,15}$/', $phoneNumber)) {
        $errors[] = 'Format nomor telepon tidak valid (gunakan 10–15 digit angka).';
    }

    if (!empty($postalCode) && !preg_match('/^[0-9]{5}$/', $postalCode)) {
        $errors[] = 'Kode pos harus terdiri dari 5 digit angka.';
    }

    // 3. Validasi Whitelist Metode Pengiriman
    $allowedShipping = ['regular', 'express'];
    if (!in_array($shippingMethod, $allowedShipping, true)) {
        $errors[] = 'Metode pengiriman tidak valid.';
    }

    // 4. Validasi Whitelist & Detail Metode Pembayaran
    $allowedPayments = ['card', 'bank_transfer', 'ewallet'];
    if (!in_array($paymentMethod, $allowedPayments, true)) {
        $errors[] = 'Metode pembayaran tidak valid.';
    }

    $paymentChannel = null;
    $accountNumber  = null;
    $accountHolder  = null;
    $expiryDate     = null;
    $cvv            = null;

    if ($paymentMethod === 'card') {
        $paymentChannel = trim($postData['card_brand'] ?? '');
        $accountNumber  = trim($postData['card_number'] ?? '');
        $accountHolder  = trim($postData['cardholder_name'] ?? '');
        $expiryDate     = trim($postData['expiry_date'] ?? '');
        $cvv            = trim($postData['card_cvv'] ?? '');

        if (empty($paymentChannel) || empty($accountNumber) || empty($accountHolder) || empty($expiryDate) || empty($cvv)) {
            $errors[] = 'Harap lengkapi detail kartu kredit/debit Anda (nomor kartu, nama pemilik, expiry date, dan CVV).';
        } else {
            if (!preg_match('/^(0[1-9]|1[0-2])\/([0-9]{2}|[0-9]{4})$/', $expiryDate)) {
                $errors[] = 'Format expiry date kartu tidak valid (gunakan MM/YY, contoh: 11/26).';
            }

            if (!preg_match('/^[0-9]{3,4}$/', $cvv)) {
                $errors[] = 'Kode CVV kartu harus terdiri dari 3 atau 4 digit angka.';
            }
        }
    } elseif ($paymentMethod === 'bank_transfer') {
        $paymentChannel = trim($postData['bank_va'] ?? '');
        $accountHolder  = $fullName;

        if (empty($paymentChannel)) {
            $errors[] = 'Harap pilih bank tujuan transfer.';
        }
    } elseif ($paymentMethod === 'ewallet') {
        $paymentChannel = trim($postData['ewallet_provider'] ?? '');
        $accountNumber  = trim($postData['ewallet_phone'] ?? '');
        $accountHolder  = $fullName;

        if (empty($paymentChannel) || empty($accountNumber)) {
            $errors[] = 'Harap pilih penyedia e-wallet dan isi nomor telepon Anda.';
        }
    }

    // 5. Validasi Keamanan File Bukti Pembayaran (Jika Diunggah)
    if (isset($fileData['payment_proof']) && $fileData['payment_proof']['error'] !== UPLOAD_ERR_NO_FILE) {
        $file = $fileData['payment_proof'];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'Terjadi kesalahan saat mengunggah bukti pembayaran.';
        } else {
            $maxSize = 2 * 1024 * 1024;
            if ($file['size'] > $maxSize) {
                $errors[] = 'Ukuran file bukti pembayaran maksimal 2MB.';
            }

            $allowedExt   = ['jpg', 'jpeg', 'png', 'pdf'];
            $allowedMimes = ['image/jpeg', 'image/png', 'application/pdf'];
            $fileExt      = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $finfo        = new finfo(FILEINFO_MIME_TYPE);
            $realMime     = $finfo->file($file['tmp_name']);

            if (!in_array($fileExt, $allowedExt, true) || !in_array($realMime, $allowedMimes, true)) {
                $errors[] = 'Format bukti pembayaran harus berupa JPG, PNG, atau PDF.';
            }
        }
    }

    return [
        'errors' => $errors,
        'data'   => [
            'full_name'       => $fullName,
            'phone_number'    => $phoneNumber,
            'address'         => $address,
            'city'            => $city,
            'province'        => $province,
            'postal_code'     => $postalCode,
            'shipping_method' => $shippingMethod,
            'payment_method'  => $paymentMethod,
            'payment_channel' => $paymentChannel,
            'account_number'  => $accountNumber,
            'account_holder'  => $accountHolder,
            'expiry_date'     => $expiryDate,
            'cvv'             => $cvv,
        ],
    ];
}

function validateCartAndStock(array $cartItems): array
{
    $errors = [];
    $orderItems = [];
    $totalProductAmount = 0;

    if (empty($cartItems)) {
        return [
            'errors'       => ['Keranjang belanja Anda masih kosong.'],
            'items'        => [],
            'total_amount' => 0,
        ];
    }

    foreach ($cartItems as $cartItem) {
        $pId = (int) ($cartItem['product_id'] ?? 0);
        $qty = (int) ($cartItem['quantity'] ?? 0);

        if ($qty <= 0) {
            $errors[] = 'Jumlah pembelian produk tidak valid.';
            break;
        }

        $product = getProduct($pId);
        if (!$product) {
            $errors[] = "Produk dengan ID $pId tidak ditemukan.";
            break;
        }

        if ($product['stock'] < $qty) {
            $errors[] = "Stok untuk produk {$product['name']} tidak mencukupi (Sisa stok: {$product['stock']}).";
            break;
        }

        $subtotal = (int) $product['price'] * $qty;
        $totalProductAmount += $subtotal;

        $orderItems[] = [
            'product_id' => $pId,
            'quantity'   => $qty,
            'price'      => (int) $product['price'],
            'subtotal'   => $subtotal,
        ];
    }

    return [
        'errors'       => $errors,
        'items'        => $orderItems,
        'total_amount' => $totalProductAmount,
    ];
}

function validateOrderStatusFilter(?string $status): string
{
    $normalized     = strtolower(trim((string) ($status ?? 'all')));
    $allowedFilters = ['all', 'pending', 'processing', 'shipped', 'completed', 'cancelled'];

    return in_array($normalized, $allowedFilters, true) ? $normalized : 'all';
}

function validateCancelOrder(?array $order, int $userId): ?string
{
    if (!$order) {
        return 'Pesanan tidak ditemukan.';
    }

    if ((int) ($order['user_id'] ?? 0) !== $userId) {
        return 'Anda tidak memiliki akses untuk membatalkan pesanan ini.';
    }

    $currentStatus = strtolower(trim((string) ($order['order_status'] ?? '')));
    if ($currentStatus !== 'pending') {
        return 'Hanya pesanan dengan status Pending yang dapat dibatalkan.';
    }

    return null;
}
